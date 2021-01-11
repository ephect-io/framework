<?php
namespace Ephel\Template;

use FunCom\Element;
use FunCom\Cache\Cache;
use FunCom\Registry\Registry;
use Ephel\Web\UI\CodeGeneratorTrait;
use Ephel\Web\UI\CustomControl;
use Ephel\Web\WebObjectInterface;
use Ephel\Xml\XmlDocument;

abstract class CustomTemplate extends CustomControl
{
    use CodeGeneratorTrait {
        writeDeclarations as private;
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

        $doc = new XmlDocument($this->viewHtml);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->getMethod() === 'extends') {

            $masterFilename = $firstMatch->properties('template');
            $masterViewName = pathinfo($masterFilename, PATHINFO_FILENAME);
            $masterHtml = file_get_contents($fullViewDir . $masterFilename);

            $masterDoc = new XmlDocument($masterHtml);
            $masterDoc->matchAll();

            $this->viewHtml = $masterDoc->replaceMatches($doc, $this->viewHtml);

            $masterHead = $this->getStyleSheetTag($masterViewName, false);
            $masterScript = $this->getScriptTag($masterViewName, false);

            if ($masterHead !== null) {
                $this->appendToHead($masterHead, $this->viewHtml);
            }
            if ($masterScript !== null) {
                $this->appendToBody($masterScript, $this->viewHtml);
            }

            $doc = new XmlDocument($this->viewHtml);
            $doc->matchAll();

        }

        if ($doc->getCount() > 0) {
            $declarations = $this->writeDeclarations($doc, $this);
            $this->creations = $declarations->creations;
            $this->additions = $declarations->additions;
            $this->afterBinding = $declarations->afterBinding;
            $this->viewHtml = $this->writeHTML($doc, $this);
        }

        Registry::setHtml($this->getUID(), $this->viewHtml);

        if (!Registry::exists('code', $this->getUID())) {
            self::getLogger()->debug('NO NEED TO WRITE CODE: ' . $this->controllerFileName, __FILE__, __LINE__);
            return false;
        }

        $code = Registry::getCode($this->getUID());
        // We store the parsed code in a file so that we know it's already parsed on next request.
        $code = str_replace(CREATIONS_PLACEHOLDER, $this->creations, $code);
        $code = str_replace(ADDITIONS_PLACEHOLDER, $this->additions, $code);
        if (!$this->isFatherTemplate() || $this->isClientTemplate()) {
            $code = str_replace(HTML_PLACEHOLDER, $this->viewHtml, $code);
        }
        $code = str_replace(DEFAULT_CONTROLLER, DEFAULT_CONTROL, $code);
        $code = str_replace(DEFAULT_PARTIAL_CONTROLLER, DEFAULT_PARTIAL_CONTROL, $code);
        $code = str_replace(CONTROLLER, CONTROL, $code);
        $code = str_replace(PARTIAL_CONTROLLER, PARTIAL_CONTROL, $code);
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

    /**
     * Load the controller file, parse it in search of namespace and classname.
     * Alternatively execute the code if the class is not already declared
     *
     * @param string $filename The controller filename
     * @param int $params The bitwise constants values that determine the behavior
     *                    INCLUDE_FILE : execute the code
     *                    RETURN_CODE : ...
     * @return boolean
     */
    public static function includeTemplateClass(CustomTemplate $template, $params = 0): ?array
    {
        $filename = $template->getControllerFileName();
        $classFilename = SRC_ROOT . $filename;
        if (!file_exists($classFilename)) {
            $classFilename = SITE_ROOT . $filename;
        }
        if (!file_exists($classFilename)) {
            return null;
        }

        list($namespace, $className, $code) = Element::getClassDefinition($classFilename);

        $fqClassName = trim($namespace) . "\\" . trim($className);

        $file = str_replace('\\', '_', $fqClassName) . '.php';

        if (isset($params) && ($params && RETURN_CODE === RETURN_CODE)) {
            $code = substr(trim($code), 0, -2) . PHP_EOL . CONTROL_ADDITIONS;
            Registry::setCode($template->getUID(), $code);
        }

        self::getLogger()->debug(__METHOD__ . '::' . $filename, __FILE__, __LINE__);

        if ((isset($params) && ($params && INCLUDE_FILE === INCLUDE_FILE)) && !class_exists('\\' . $fqClassName)) {
            if (\Phar::running() != '') {
                include pathinfo($filename, PATHINFO_BASENAME);
            } else {
                //include $classFilename;
            }
        }

        return [$classFilename, $fqClassName, $code];
    }



    public static function import(CustomControl $ctrl, string $className): bool
    {
        if (!isset($className)) {
            $className = $ctrl->getClassName();
        }
        $result = false;
        $file = '';
        $type = '';
        $code = '';

        $cacheFilename = '';
        //$classFilename = '';
        $cacheJsFilename = '';
        $viewName = '';

        $info = Registry::classInfo($className);
        self::getLogger()->dump('CLASS INFO::' . $className, $info, __FILE__, __LINE__);

        if ($info !== null) {
            $viewName = Element::innerClassNameToFilename($className);
            $path = PHINK_VENDOR_LIB . $info->path;

            if ($info->path[0] == '@') {
                $path = str_replace("@" . DIRECTORY_SEPARATOR, PHINK_VENDOR_APPS, $info->path);
            }
            if ($info->path[0] == '~') {
                $path = str_replace("~" . DIRECTORY_SEPARATOR, PHINK_VENDOR_WIDGETS, $info->path);
            }

            $cacheFilename = Cache::cacheFilenameFromView($viewName, $ctrl->isInternalComponent());
        }
        if ($info === null) {
            $viewName = self::userClassNameToFilename($className);

            //$classFilename = 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $className . CLASS_EXTENSION;
            $cacheFilename = Cache::cacheFilenameFromView($viewName, $ctrl->isInternalComponent());
            self::getLogger()->debug('CACHED JS FILENAME: ' . $cacheJsFilename, __FILE__, __LINE__);
        }
        $cacheJsFilename = Cache::cacheJsFilenameFromView($viewName, $ctrl->isInternalComponent());
        $cacheCssFilename = Cache::cacheCssFilenameFromView($viewName, $ctrl->isInternalComponent());

        if (file_exists(SRC_ROOT . $cacheFilename)) {

            if (file_exists(DOCUMENT_ROOT . $cacheJsFilename)) {
                $ctrl->appendJsToBody($viewName);

                self::getLogger()->debug('INCLUDE CACHED JS CONTROL: ' . DOCUMENT_ROOT . $cacheJsFilename, __FILE__, __LINE__);
                $ctrl->getResponse()->addScript($cacheJsFilename);
            }
            self::getLogger()->debug('INCLUDE CACHED CONTROL: ' . SRC_ROOT . $cacheFilename, __FILE__, __LINE__);
            // self::includeClass($cacheFilename, RETURN_CODE);

            include SRC_ROOT . $cacheFilename;

            return true;
        }

        $include = null;
        //            $modelClass = ($include = Autoloader::includeModelByName($viewName)) ? $include['type'] : DEFALT_MODEL;
        //            include SRC_ROOT . $include['file'];
        //            $model = new $modelClass();


        self::getLogger()->debug('PARSING ' . $viewName . '!!!');
        $view = new PartialTemplate($ctrl, $className);

        if ($info !== null) {
            list($file, $type, $code) = CustomTemplate::includeInnerClass($view, $info);
            $view->getCacheFilename();
        } else {
            list($file, $type, $code) = CustomTemplate::includeTemplateClass($view, RETURN_CODE);
        }
        Registry::setCode($view->getUID(), $code);
        self::getLogger()->debug($view->getControllerFileName() . ' IS REGISTERED : ' . (Registry::exists('code', $view->getControllerFileName()) ? 'TRUE' : 'FALSE'), __FILE__, __LINE__);
        self::getLogger()->debug('CONTROLLER FILE NAME OF THE PARSED VIEW: ' . $view->getControllerFileName());
        $view->parse();

        self::getLogger()->debug('CACHE FILE NAME OF THE PARSED VIEW: ' . $view->getCacheFileName());
        self::getLogger()->debug('ROOT CACHE FILE NAME OF THE PARSED VIEW: ' . SRC_ROOT . $cacheFilename);

        include SRC_ROOT . $cacheFilename;

        return true;
    }


    private static function includeInnerClass(CustomTemplate $view, object $info, bool $withCode = true): array
    {
        $className = $view->getClassName();
        $viewName = $view->getViewName();

        // $filename = $info->path . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR  . \Ephel\Autoloader::innerClassNameToFilename($className) . CLASS_EXTENSION;
        // $filename = $view->getControllerFileName();
        $filename = $info->path . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR  . $viewName . CLASS_EXTENSION;

        if ($filename[0] == '@') {
            $filename = \str_replace('@/', PHINK_APPS_ROOT, $filename);
        }
        if ($filename[0] == '~') {
            $filename = \str_replace('~/', PHINK_WIDGETS_ROOT, $filename);
        }
        //self::getLogger()->debug('INCLUDE INNER PARTIAL CONTROLLER : ' . $filename, __FILE__, __LINE__);

        $code = file_get_contents($filename, FILE_USE_INCLUDE_PATH);

        if ($withCode) {
            $code = substr(rim($code), 0, -2) . PHP_EOL . CONTROL_ADDITIONS;
            Registry::setCode($view->getUID(), $code);
        }

        return [$filename, $info->namespace . '\\' . $className, $code];
    }
}
