<?php

namespace Ephel\Web\UI;

use Ephel\Template\CustomTemplate;
use FunCom\Components\Generators\ComponentDocument;

/**
 * Description of code_generator
 *
 * @author David
 */
trait CodeGeneratorTrait
{
    private $_reservedDeclarationsKeywords = ['Page', 'Echo', 'Exec', 'Type', 'Block', 'Extends'];
    private $_reservedHtmlKeywords = ['Echo', 'Exec', 'Render', 'Block'];

    public function writeHTML(ComponentDocument $doc, CustomTemplate $parentTemplate)
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
