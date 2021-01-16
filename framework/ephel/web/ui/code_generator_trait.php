<?php

namespace Ephel\Web\UI;

use Exception;
use FunCom\Cache\Cache;
use FunCom\Registry\Registry;
use Ephel\Template\CustomTemplate;
use Ephel\Template\PartialTemplate;
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

    public function writeDeclarations(ComponentDocument $doc, CustomTemplate $parentTemplate)
    {

        $result = '';
        $dictionary = $parentTemplate->getDictionary();
        $matches = $doc->getDepthsOfMatches();
        $docList = $doc->getList();
        $count = count($docList);
        $uid = $parentTemplate->getUID();

        $code = '';
        $uses = [];
        $requires = [];
        $creations = [];
        $additions = [];
        $afterBinding = [];

        $childName = [];
        $childrenIndex = [];

        self::getLogger()->debug('DOC LIST');
        self::getLogger()->debug($docList);

        $isFirst = true;
        foreach ($docList as $control) {
            if (in_array($control['name'], $this->_reservedDeclarationsKeywords) || $control['method'] == 'render') {
                continue;
            }

            $className = '';
            $nameSpace = '';
            $classPath = '';
            $templatePath = '';
            $j = $control['id'];

            if (isset($control['properties'])) {
                $parentId = $control['parentId'];
                $parentControl = ($parentId > -1) ? $docList[$parentId] : [];
                if (isset($parentControl['childName'])) {
                    $parentChildName = $parentControl['childName'];
                    $controlName = $control['name'];

                    if ($parentChildName == $controlName) {
                        if (!isset($childrenIndex["$parentId"])) {
                            $childrenIndex[$parentId] = 0;
                        } else {
                            $childrenIndex[$parentId] = $childrenIndex[$parentId] + 1;
                        }
                    }
                }

                $properties = $control['properties'];

                try {
                    $controlId = isset($properties['id']) ? $properties['id'] : null;
                    if ($controlId === null) {
                        throw new Exception("This control has no 'id' attribute. Please provide one.");
                    }
                } catch (Exception $ex) {
                    self::getLogger()->exception($ex);
                }

                //$className = ucfirst($control['name']);
                $className = $control['name'];
                $fqcn = '';
                $code  = '';
                $info = Registry::classInfo($className);
                //self::$logger->dump('REGISTRY INFO ' . $className, $info);
                if ($info) {
                    if (!$info->isAutoloaded) {
                        array_push($requires, '\\Ephel\\CustomTemplate::import($this, "' . $className . '");');
                        //                        array_push($requires, '$this->import("' . $className . '");');
                    }
                    $fqcn = $info->namespace . '\\' . $className;
                } elseif ($className !== 'this') {
                    $viewName = CustomTemplate::userClassNameToFilename($className);
                    $view = new PartialTemplate($parentTemplate, [], $className);
                    $view->setNames();
                    $fullClassPath = $view->getControllerFileName();
                    $fullJsClassPath = $view->getJsControllerFileName();

                    $fullJsCachePath = Cache::cacheJsFilenameFromView($viewName, $parentTemplate->isInternalComponent());
                    array_push($requires, '\\Ephel\\CustomTemplate::import($this, "' . $className . '");');

                    list($file, $fqcn, $code) = CustomTemplate::includeClass($fullClassPath, RETURN_CODE);

                    if (file_exists(DOCUMENT_ROOT . $fullJsCachePath)) {
                        $parentTemplate->getResponse()->addScriptFirst($fullJsCachePath);
                    }
                    if (file_exists(SRC_ROOT . $fullJsClassPath)) {
                        copy(SRC_ROOT . $fullJsClassPath, DOCUMENT_ROOT . $fullJsCachePath);
                        $parentTemplate->getResponse()->addScriptFirst($fullJsCachePath);
                    }
                    if (file_exists(SITE_ROOT . $fullJsClassPath)) {
                        copy(SITE_ROOT . $fullJsClassPath, DOCUMENT_ROOT . $fullJsCachePath);
                        $parentTemplate->getResponse()->addScriptFirst($fullJsCachePath);
                    }
                    Registry::setCode($view->getUID(), $code);
                }

                $canRender = ($info && $info->canRender || !$info);
                $notThis = ($className != 'this');
                $serialize = $canRender && $notThis;
                $serialize = false;
                $index = '';
                if (isset($childrenIndex[$parentId])) {
                    $index = '[' . $childrenIndex[$parentId] . ']';
                }

                $thisControl = '$this' . (($notThis) ? '->' . $controlId . $index : '');

                if ($isFirst) {
                    $isFirst = false;
                }

                foreach ($properties as $key => $value) {
                    if ($key == 'id') {
                        continue;
                    }
                    if (is_numeric($value)) {
                        continue;
                    }
                    if (strpos($value, ':') > -1) {
                        continue;
                    }
                    if (!empty(strstr($value, '!#base64#'))) {
                        continue;
                    }
                    // if ($key == 'command') {
                    //     array_push($afterBinding[$j], $thisControl . '->set' . ucfirst($key) . '("' . $value . '"); ');
                    // } else {
                    array_push($additions[$j], $thisControl . '->set' . ucfirst($key) . '("' . $value . '"); ');
                    // }
                }

            }


            $method = $docList[$j]['method'];
            // if ((Registry::classInfo($method) && Registry::classCanRender($method)) || !Registry::classInfo($method)) {
            //     $doc->fieldValue($j, 'method', 'render');
            // }
        }

        return (object) ['creations' => '', 'additions' => '', 'afterBinding' => ''];
    }

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
