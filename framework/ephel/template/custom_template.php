<?php
namespace Ephel\Template;

use FunCom\Element;
use FunCom\Cache\Cache;
use FunCom\Registry\Registry;
use FunCom\Components\Generators\ComponentDocument;
use Ephel\Web\UI\CodeGeneratorTrait;
use Ephel\Web\UI\CustomControl;
use Ephel\Web\WebObjectInterface;

abstract class CustomTemplate extends CustomControl
{
    use CodeGeneratorTrait {
        writeHTML as private;
    }

    protected $router = null;
    protected $viewHtml = '';
    protected $twigHtml = '';
    protected $preHtml = '';
    protected $designs = array();
    protected $design = '';
    protected $creations = '';
    protected $additions = '';
    protected $afterBinding = '';
    protected $modelIsIncluded = false;
    protected $controllerIsIncluded = false;
    protected $pattern = '';
    protected $depth = 0;
    protected $viewIsFather = false;
    protected $engineIsEphel = true;
    protected $engineIsTwig = false;
    protected $dictionary = [];

    function __construct(WebObjectInterface $parent, array $dictionary)
    {
        parent::__construct($parent);

        $this->clonePrimitivesFrom($parent);

        //$this->redis = new Client($this->context->getRedis());

        $this->dictionary = $dictionary;
        $uid = $this->getUID();
        Registry::write('template', $uid, $dictionary);
    }

    function isFatherTemplate(): bool
    {
        return $this->viewIsFather;
    }

    function getDictionary(): ?array
    {
        return $this->dictionary;
    }

    function isEphelEngine(): bool
    {
        return $this->engineIsEphel;
    }

    function isTwigEngine(): bool
    {
        return $this->engineIsTwig;
    }

    function getDepth(): int
    {
        return $this->depth;
    }
    function setDepth($value): void
    {
        $this->depth = $value;
    }

    function getCreations(): string
    {
        return $this->creations;
    }

    function getAdditions(): string
    {
        return $this->additions;
    }

    function getAfterBinding(): string
    {
        return $this->afterBinding;
    }

    // public function setViewHtml($html)
    // {
    //     $this->viewHtml = $html;
    // }

    function getViewHtml(): string
    {
        return $this->viewHtml;
    }

    function setTwigHtml($html): void
    {
        $this->twigHtml = $html;
    }

    function getTwigHtml(): string
    {
        return $this->twigHtml;
    }

    function loadView($filename): string
    {
        $lines = file($filename);
        $text = '';
        foreach ($lines as $line) {
            // $text .= trim($line) . PHP_EOL;
            $text .= $line;
        }

        return $text;
    }

    function parse(): bool
    {

        $baseViewDir = SRC_ROOT;

        $fullViewDir = $baseViewDir . pathinfo($this->viewFileName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        
        $this->viewHtml = file_get_contents($fullViewDir . $this->viewName . PREHTML_EXTENSION);

        $head = $this->getStyleSheetTag();
        $script = $this->getScriptTag();

        if ($this->isFatherTemplate()) {
            if ($head !== null) {
                Registry::push($this->getFatherUID(), 'head', $head);
                $this->appendToHead($head, $this->viewHtml);
            }
            if ($script !== null) {
                Registry::push($this->getFatherUID(), 'scripts', $script);
                $this->appendToBody($script, $this->viewHtml);
            }
        }

        $doc = new ComponentDocument($this->viewHtml);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->hasCloser()) {

            // TODO: use ViewRegistry instead ...
            $parentFilename = strtolower($firstMatch->getName()) . PREHTML_EXTENSION;

            $parentViewName = pathinfo($parentFilename, PATHINFO_FILENAME);
            $parentHtml = file_get_contents($fullViewDir . $parentFilename);

            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $this->viewHtml = $parentDoc->replaceMatches($doc, $this->viewHtml);

            $parentHead = $this->getStyleSheetTag($parentViewName, false);
            $parentScript = $this->getScriptTag($parentViewName, false);

            if ($parentHead !== null) {
                $this->appendToHead($parentHead, $this->viewHtml);
            }
            if ($parentScript !== null) {
                $this->appendToBody($parentScript, $this->viewHtml);
            }

            $doc = new ComponentDocument($this->viewHtml);
            $doc->matchAll();

        }

        if ($doc->getCount() > 0) {
            $this->viewHtml = $this->writeHTML($doc, $this);
        }

        Registry::setHtml($this->getUID(), $this->viewHtml);

        if (!Registry::exists('code', $this->getUID())) {
            self::getLogger()->debug('NO NEED TO WRITE CODE: ' . $this->controllerFileName, __FILE__, __LINE__);
            return false;
        }

        $code = Registry::getCode($this->getUID());
        // We store the parsed code in a file so that we know it's already parsed on next request.
        if (!$this->isFatherTemplate() || $this->isClientTemplate()) {
            
            // $code = str_replace(HTML_PLACEHOLDER, $this->viewHtml, $code);

        }
        if (!empty(trim($code))) {
            self::getLogger()->debug('SOMETHING TO CACHE : ' . $this->getCacheFileName(), __FILE__, __LINE__);
            if (!$this->isFatherTemplate()) {
                file_put_contents($this->getCacheFileName(), $code);
            }
            Registry::setCode($this->getUID(), $code);
        }

        $this->engineIsEphel = true;
        // $this->redis->mset($this->preHtmlName, $this->declarations . $this->viewHtml);


        // We generate the code, but we don't flag it as parsed because it was not "executed"
        return false;
    }

    function safeCopy(string $filename, string $cacheFilename): bool
    {
        $ok = false;
        $src = SRC_ROOT . $filename;
        $dest = DOCUMENT_ROOT . $cacheFilename;

        if (!file_exists($src)) {
            $src = SITE_ROOT . $filename;
        }

        if (file_exists($src)) {
            $ok = file_exists($dest);
            if (!$ok) {
                $ok = copy($src, $dest);
            }
        }

        return $ok;
    }

    function getScriptTag(?string $viewName = null, ?bool $isInternal = null): ?string
    {
        $jsControllerFileName = '';

        if ($viewName !== null) {
            $mvc = $this->getMvcFileNamesByViewName($viewName);
            $jsControllerFileName = $mvc['jsControllerFileName'];
        }

        if ($viewName === null) {
            $jsControllerFileName = $this->getJsControllerFileName();
            $viewName = $this->getViewName();
        }
        if ($isInternal === null) {
            $isInternal = $this->isInternalComponent();
        }

        $cacheJsFilename = Cache::cacheJsFilenameFromView($viewName, $isInternal);
        $script = "<script src='" . Cache::absoluteURL($cacheJsFilename) . "'></script>" . PHP_EOL;

        $ok = $this->safeCopy($jsControllerFileName, $cacheJsFilename);

        return ($ok) ? $script : null;
    }

    function getStyleSheetTag(?string $viewName = null, ?bool $isInternal = null): ?string
    {
        $cssFileName = '';

        if ($viewName !== null) {
            $mvc = $this->getMvcFileNamesByViewName($viewName);
            $cssFileName = $mvc['cssFileName'];
        }

        if ($viewName === null) {
            $cssFileName = $this->getCssFileName();
            $viewName = $this->getViewName();
        }
        if ($isInternal === null) {
            $isInternal = $this->isInternalComponent();
        }

        $cacheCssFilename = Cache::cacheCssFilenameFromView($viewName, $isInternal);
        $head = "<link rel='stylesheet' href='" . Cache::absoluteURL($cacheCssFilename) . "' />" . PHP_EOL;

        $ok = $this->safeCopy($cssFileName, $cacheCssFilename);

        return ($ok) ? $head : null;
    }

    function appendToBody(string $scripts, string &$viewHtml): void
    {
        if ($scripts !== '') {
            $scripts .= '</body>' . PHP_EOL;
            $viewHtml = str_replace('</body>', $scripts, $viewHtml);
        }
    }

    function appendToHead(string $head, string &$viewHtml): void
    {
        if ($head !== '') {
            $head .= '</head>' . PHP_EOL;
            $viewHtml = str_replace('</head>', $head, $viewHtml);
        }
    }

}
