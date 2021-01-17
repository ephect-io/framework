<?php
namespace Ephel\Template;

use FunCom\Registry\Registry;
use FunCom\Components\Generators\ComponentDocument;
use Ephel\Web\UI\CodeGeneratorTrait;
use Ephel\Web\WebObject;
use Ephel\Web\WebObjectInterface;

class Template extends WebObject implements TemplateInterface
{
    protected $router = null;
    protected $viewHtml = '';
    protected $preHtml = '';
    protected $designs = array();
    protected $depth = 0;
    protected $viewIsFather = false;
    protected $dictionary = [];

    private $_reservedHtmlKeywords = ['Echo', 'Exec', 'Render', 'Block'];

    function __construct(WebObjectInterface $parent, array $dictionary)
    {
        parent::__construct($parent);

        $this->clonePrimitivesFrom($parent);

        //$this->redis = new Client($this->context->getRedis());

        $this->dictionary = $dictionary;
        $uid = $this->getUID();
        Registry::write('template', $uid, $dictionary);

        $this->viewName = $parent->getViewName();

        $this->clonePrimitivesFrom($parent);
        $this->cloneNamesFrom($parent);
        $this->getCacheFileName();
        $this->cacheFileName = $parent->getCacheFileName();
        $this->fatherTemplate = $this;
        $this->viewIsFather = true;
        $this->fatherUID = $this->getUID();

    }

    function isFatherTemplate(): bool
    {
        return $this->viewIsFather;
    }

    function getDictionary(): ?array
    {
        return $this->dictionary;
    }

    function getDepth(): int
    {
        return $this->depth;
    }
    function setDepth($value): void
    {
        $this->depth = $value;
    }

    function getViewHtml(): string
    {
        return $this->viewHtml;
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

        $doc = new ComponentDocument($this->viewHtml);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->hasCloser()) {

            // TODO: use ViewRegistry instead ...
            $parentFilename = strtolower($firstMatch->getName()) . PREHTML_EXTENSION;

            $parentHtml = file_get_contents($fullViewDir . $parentFilename);

            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $this->viewHtml = $parentDoc->replaceMatches($doc, $this->viewHtml);

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
        if (!empty(trim($code))) {
            if (!$this->isFatherTemplate()) {
                file_put_contents($this->getCacheFileName(), $code);
            }
            Registry::setCode($this->getUID(), $code);
        }

        // We generate the code, but we don't flag it as parsed because it was not "executed"
        return false;
    }

    public function writeHTML(ComponentDocument $doc, Template $parentTemplate)
    {
        $dictionary = $parentTemplate->getDictionary();
        $viewHtml = $parentTemplate->getViewHtml();
        $uid = $parentTemplate->getUID();

        $count = $doc->getCount();
        $matchesByDepth = $doc->getDepthsOfMatches();
        $matchesById = $doc->getIDsOfMatches();
        $matchesByKey = $doc->getKeysOfMatches();

        for ($i = $count - 1; $i > -1; $i--) {
            $j = $matchesById[$i];
            $match = $doc->getMatchById($j);

            $tag = $match->getMethod();
            $name = $match->getName();

            if (!in_array($tag, $this->_reservedHtmlKeywords)) {
                continue;
            }

            $type = $match->properties('type');
            $class = $match->properties('class');
            $id = $match->properties('id');

            $const = $match->properties('const');
            $var = $match->properties('var');
            $prop = $match->properties('prop');
            $stmt = $match->properties('stmt');
            $params = $match->properties('params');
            $content = $match->getContents();

            if (!$type || $type == 'this') {
                $type = '$this->';
            } elseif ($type == 'none') {
                $type = '';
            } else {
                $type = $type . '::' . (($tag == 'exec') ? '' : '$');
            }

            if ($tag == 'Echo' && $const) {
                $declare = '<?php echo ' . $const . '; ?>';
            } elseif ($tag == 'Echo' && $var) {
                /** $declare = '<?php echo ' . $type . $var . '; ?>';  */

                $declare = '<?php echo \\FunCom\\Registry\\Registry::read("template", "' . $uid . '")["' . $var . '"];?>';
            } elseif ($tag == 'Echo' && $prop) {
                $declare = '<?php echo ' . $type . 'get' . ucfirst($prop) . '(); ?>';
            } elseif ($tag == 'Exec') {
                $declare = '<?php echo ' . $type . $stmt . '(); ?>';
                if ($params != null) {
                    $declare = '<?php echo ' . $type . $stmt . '(' . $params . '); ?>';
                }
            } elseif ($tag == 'Block' && null !== $content) {
                // $plaintext = substr($content, 9);
                // $plaintext = \base64_decode($plaintext);
                // $declare = $plaintext;
                $declare = $content;
            } elseif ($tag == 'Render') {
                if ($name == 'this') {
                    $declare = '<?php $this->renderHtml(); $this->renderedHtml(); ?>';
                } else {
                    /** $declare = '<?php ' . $type . $id . '->render(); ?>'; */
                    $declare = '<?php echo \\FunCom\\Registry\\Registry::read("' . $uid . '", "' . $id . '")[0];?>';
                }
            }

            $viewHtml = $doc->replaceThisMatch($match, $viewHtml, $declare);
        }
        return $viewHtml;
    }

}
