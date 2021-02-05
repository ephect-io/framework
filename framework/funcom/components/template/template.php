<?php

namespace FunCom\Template;

use FunCom\Registry\Registry;
use FunCom\Components\Generators\ComponentDocument;
use FunCom\Element;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;
use FunCom\Web\TemplateInterface;
use FunCom\Web\TemplateTrait;

class Template extends Element implements TemplateInterface
{
    use TemplateTrait;

    protected $viewHtml = '';
    protected $preHtml = '';
    protected $depth = 0;
    protected $viewIsFather = false;
    protected $dictionary = [];

    private $_reservedHtmlKeywords = ['Echo', 'Exec', 'Render', 'Block'];

    function __construct(TemplateInterface $parent, array $dictionary)
    {
        parent::__construct($parent);

        UseRegistry::uncache();
        ViewRegistry::uncache();

        $this->dictionary = $dictionary;
        $uid = $this->getUID();
        Registry::write('template', $uid, $dictionary);

        $this->viewName = $parent->getViewName();
        $this->className = $parent->getClassName();
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
        $this->viewFileName = ViewRegistry::read($this->className);
        $this->viewHtml = file_get_contents(SRC_ROOT . $this->viewFileName);

        $doc = new ComponentDocument($this->viewHtml);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->hasCloser()) {

            $parentClassName = UseRegistry::read($firstMatch->getName());
            $parentFilename = ViewRegistry::read($parentClassName);
            $parentHtml = file_get_contents(SRC_ROOT . $parentFilename);

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
            return false;
        }

        $code = Registry::getCode($this->getUID());
        // We store the parsed code in a file so that we know it's already parsed on next request.
        if (!empty(trim($code))) {
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
        $matchesById = $doc->getIDsOfMatches();

        for ($i = $count - 1; $i > -1; $i--) {
            $j = $matchesById[$i];
            $match = $doc->getMatchById($j);

            $tag = $match->getMethod();
            $name = $match->getName();

            if (!in_array($tag, $this->_reservedHtmlKeywords)) {
                continue;
            }

            $type = $match->properties('type');
            $id = $match->properties('id');

            $var = $match->properties('var');
            $prop = $match->properties('prop');
            $content = $match->getContents();

            if (!$type || $type == 'this') {
                $type = '$this->';
            } elseif ($type == 'none') {
                $type = '';
            } else {
                $type = $type . '::' . (($tag == 'exec') ? '' : '$');
            }

            if ($tag == 'Echo' && $var) {
                /** $declare = '<?php echo ' . $type . $var . '; ?>';  */

                $declare = '<?php echo \\FunCom\\Registry\\Registry::read("template", "' . $uid . '")["' . $var . '"];?>';
            } elseif ($tag == 'Echo' && $prop) {
                $declare = '<?php echo ' . $type . 'get' . ucfirst($prop) . '(); ?>';
            } elseif ($tag == 'Block' && null !== $content) {
                $declare = $content;
            } elseif ($tag == 'Render') {
                    $declare = '<?php echo \\FunCom\\Registry\\Registry::read("' . $uid . '", "' . $id . '")[0];?>';
            }

            $viewHtml = $doc->replaceThisMatch($match, $viewHtml, $declare);
        }
        return $viewHtml;
    }
}
