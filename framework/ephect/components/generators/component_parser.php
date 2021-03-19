<?php

namespace Ephect\Components\Generators;

use Ephect\Components\AbstractComponent;
use Ephect\Components\ComponentEntity;
use Ephect\Components\ComponentInterface;
use Ephect\Components\ComponentStructure;
use Ephect\Crypto\Crypto;
use Ephect\IO\Utils;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;

define('TERMINATOR', '/');
define('SKIP_MARK', '!');
define('QUEST_MARK', '?');

class ComponentParser
{
    protected $html = '';
    protected $comp = null;
    protected $useVariables = [];
    protected $parentHTML = '';
    protected $maker = null;
    protected $depths = [];
    protected $idListByDepth = [];
    protected $list = [];

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->html = $comp->getCode();
        $this->parentHTML = $comp->getParentHTML();
        $this->maker = new Maker($comp);
        ComponentRegistry::uncache();

    }

    public function getList(): array
    {
        return $this->list;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getDepths(): array
    {
        return $this->depths;
    }

    public function getIdListByDepth(): array
    {
        return $this->idListByDepth;
    }

    public function doComponents(): array
    {
        $result = [];

        $list = [];

        $re = '/<(\/)?([A-Z]\w+).*?>/m';
        $str = $this->html;

        preg_match_all($re, $str, $list, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

        $l = count($list);

        // Re-structure the list recursively
        for ($i = $l - 1; $i > -1; $i--) {

            $list[$i]['uid'] = Crypto::createUID();

            $list[$i]['id'] = $i;
            $list[$i]['class'] = ComponentRegistry::read($list[$i][2][0]);
            $list[$i]['component'] = $this->component->getFullyQualifiedFunction();
            $list[$i]['text'] = $list[$i][0][0];
            $list[$i]['name'] = $list[$i][2][0];
            $list[$i]['method'] = $list[$i][2][0];
            $list[$i]['startsAt'] = $list[$i][0][1];
            $list[$i]['endsAt'] = $list[$i][0][1] + strlen($list[$i][0][0]);
            $list[$i]['props'] = $this->doArguments($list[$i][0][0]);
            $list[$i]['node'] = false;
            $list[$i]['hasCloser'] = false;
            $list[$i]['isCloser'] = false;

            if ($list[$i][1][0] === '/') {
                for ($j = $i - 1; $j > -1; $j--) {
                    if ($list[$i][2][0] === $list[$j][2][0] && $list[$j][1][0] === '') {
                        $list[$j]['closer'] = [
                            'id' => $i,
                            'parentId' => $j,
                            'text' => $list[$i][0][0],
                            'name' => $list[$i][2][0],
                            'startsAt' => $list[$i][0][1],
                            'endsAt' => $list[$i][0][1] + strlen($list[$i][0][0]),
                            'contents' => ['startsAt' => $list[$j][0][1] + strlen($list[$j][0][0]), 'endsAt' => $list[$i][0][1] - 1],
                        ];
                        $list[$i]['isCloser'] = true;
                        break;
                    }
                }
            }

            if (isset($list[$i])) {
                unset($list[$i][0]);
                unset($list[$i][1]);
                unset($list[$i][2]);
            }
            if (isset($list[$i]['closer'])) {
                $list[$i]['isCloser'] = false;
                $list[$i]['hasCloser'] = true;
            }
        }

        $depth = 0;
        $parentIds = [];
        $parentIds[$depth] = -1;

        $l = count($list);

        // Add useful information in list like depth and parentId
        for ($i = 0; $i < $l; $i++) {

            $siblingId = $i - 1;

            $isSibling = isset($list[$siblingId]) && $list[$siblingId]['hasCloser'];

            $component = $list[$i]['text'];
            $firstName = $list[$i]['name'];
            $secondName = isset($list[$i + 1]) ? $list[$i + 1]['name'] : 'eof';

            if (!isset($parentIds[$depth])) {
                $parentIds[$depth] = $i - 1;
            }

            $list[$i]['isSibling'] = $isSibling;
            $list[$i]['parentId'] = $parentIds[$depth];
            $list[$i]['depth'] = $depth;

            if (TERMINATOR . $firstName != $secondName) {
                if ($list[$i]['isCloser']) {
                    $list[$i]['isSibling'] = $isSibling;

                    $pId = !$isSibling && isset($parentIds[$depth]) ? $parentIds[$depth] : $siblingId;
                    $depth--;

                    $list[$i]['parentId'] = $parentIds[$depth];
                    $list[$i]['depth'] = $depth;
                    // array_push($this->idListByDepth, $i);

                    if ($list[$pId]['isSibling']) {
                        $list[$i]['depth'] = $list[$pId]['depth'];
                    }
                } elseif ($component[1] == QUEST_MARK) {
                } elseif (false === $list[$i]['hasCloser']) {
                } elseif ($component[1] == SKIP_MARK) {
                } else {
                    if ($list[$i]['hasCloser']) {
                        $depth++;
                    }

                    $this->depths[$depth] = 1;

                    if (isset($parentIds[$depth])) {
                        unset($parentIds[$depth]);
                    }
                }
            }
        }

        for ($i = $l - 1; $i > -1; $i--) {
            // Remove useless data
            if ($list[$i]['isCloser']) {
                unset($list[$i]);
            } else {
                unset($list[$i]['isCloser']);
            }
        }

        $maxDepth = count($this->depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    array_push($this->idListByDepth, $match['id']);
                }
            }
        }

        $this->list = $list;

        return $list;
    }

    public function doArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_]*)=("([\S\\\\\" ]*)"|\'([\S\\\\\' ]*)\'|\{([\S\\\\\{\}\(\)=\<\> ]*)\})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[2], 1), 0, -1);

            $result[$key] = $value;
        }

        return $result;
    }

    public function bindNodes(): array
    {
        $list = $this->list;

        $depths = $this->getIdListByDepth();

        $c = count($list);
        for ($j = $c - 1; $j > -1; $j--) {
        // for($j = 0; $j < $c; $j++) {
            $i = $depths[$j];
            if ($list[$i]['parentId'] === -1) {
                continue;
            }
            $pId = $list[$i]['parentId'];

            if (!is_array($list[$pId]['node'])) {
                $list[$pId]['node'] = [];
            }
            array_push($list[$pId]['node'], $list[$i]);
            unset($list[$i]);
        }

        return $list;
    }


    public function copyComponents(): void
    {
        /**
         * TO DO 
         * get composition
         */
        // $funcName = $this->component->getFunction();
        $fqFuncName = $this->component->getFullyQualifiedFunction();
        $flatEnttity = CodeRegistry::read($fqFuncName);

        // $compEntity =  ComponentEntity::buildFromArray($flatEnttity);
        
        $componentList = ComponentEntity::getComposedOf($flatEnttity);

        if($componentList === null) {
            return;
        }

        foreach($componentList as $component) {
            $fqFuncName = ComponentRegistry::read($component);
            $fqFuncFile = ComponentRegistry::read($fqFuncName);
            $funcName = AbstractComponent::functionName($fqFuncName);

            if($fqFuncFile === null) {
                continue;
            }

            $fqFuncUID = ComponentRegistry::read($fqFuncFile);

            $token = '_' . str_replace('-', '', $fqFuncUID);

            $funcCopyFile = str_replace('\\', '_', strtolower($fqFuncName));
            $funcCopyFile = str_replace($funcCopyFile, $funcCopyFile . $token, $fqFuncFile);

            if(file_exists(CACHE_DIR . $funcCopyFile)) {
                continue;
            }

            $funcHtml = file_get_contents(SRC_COPY_DIR . $fqFuncFile);

            $funcToken = $funcName . $token;

            $funcHtml = str_replace($funcName, $funcToken, $funcHtml);

            Utils::safeWrite(CACHE_DIR . $funcCopyFile, $funcHtml);

        }
    }
}
