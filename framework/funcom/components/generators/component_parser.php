<?php

namespace FunCom\Components\Generators;

use FunCom\Components\ComponentInterface;

define('TERMINATOR', '/');
define('SKIP_MARK', '!');
define('QUEST_MARK', '?');

class ComponentParser
{

    protected $html = '';
    protected $view = null;
    protected $useVariables = [];
    protected $parentHTML = '';
    protected $maker = null;

    public function __construct(ComponentInterface $view)
    {
        $this->view = $view;
        $this->html = $view->getCode();
        $this->parentHTML = $view->getParentHTML();
        $this->maker = new Maker($view);
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function doComponents(): array
    {
        $result = [];

        $re = '/<(\/)?([A-Z]\w+).*?>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);


        $l = count($matches);

        // Re-structure the matches recursively
        for ($i = $l - 1; $i > -1; $i--) {

            $matches[$i]['id'] = $i;
            $matches[$i]['component'] = $matches[$i][0][0];
            $matches[$i]['name'] = $matches[$i][2][0];
            $matches[$i]['startsAt'] = $matches[$i][0][1];
            $matches[$i]['props'] = $this->doArguments($matches[$i][0][0]);
            $matches[$i]['hasCloser'] = false;
            $matches[$i]['isCloser'] = false;

            if ($matches[$i][1][0] === '/') {
                for ($j = $i - 1; $j > -1; $j--) {
                    if ($matches[$i][2][0] === $matches[$j][2][0] && $matches[$j][1][0] === '') {
                        $matches[$j]['closer'] = [
                            'id' => $i,
                            'parentId' => $j,
                            'component' => $matches[$i][0][0],
                            'name' => $matches[$i][2][0],
                            'startsAt' => $matches[$i][0][1],
                        ];
                        $matches[$i]['isCloser'] = true;
                        break;
                    }
                }
            }

            if (isset($matches[$i])) {
                unset($matches[$i][0]);
                unset($matches[$i][1]);
                unset($matches[$i][2]);
            }
            if (isset($matches[$i]['closer'])) {
                $matches[$i]['isCloser'] = false;
                $matches[$i]['hasCloser'] = true;
            }
        }

        // Reindex the matches
        $list = [];
        foreach ($matches as $key => $value) {
            array_push($list, $value);
        }

        $depth = 0;
        $parentIds = [];
        $parentIds[$depth] = -1;
        $l = count($list);

        // Add useful information in matches like depth and parentId
        for ($i = 0; $i < $l; $i++) {
            $siblingId = $i - 1;

            $isSibling = isset($list[$siblingId]) && $list[$siblingId]['hasCloser'];

            $s = $list[$i]['component'];
            $firstName = $list[$i]['name'];

            $hasCloser = $list[$i]['hasCloser'];

            $secondName = isset($list[$i + 1]) ? $list[$i + 1]['name'] : 'eof';
            if (!isset($parentIds[$depth])) {
                $parentIds[$depth] = $i - 1;
            }
            $list[$i]['isSibling'] = $isSibling;
            $list[$i]['parentId'] = $parentIds[$depth];

            $list[$i]['depth'] = $depth;

            if (TERMINATOR . $firstName != $secondName) {
                if ($s[1] == TERMINATOR) {
                    $list[$i]['isSibling'] = $isSibling;

                    $pId = !$isSibling && isset($parentIds[$depth]) ? $parentIds[$depth] : $siblingId;
                    $depth--;
                    $fatherId = $parentIds[$depth];

                    $list[$i]['parentId'] = $fatherId;
                    $list[$i]['depth'] = $depth;

                    $list[$i]['depth'] = $list[$i]['depth'];

                    if ($list[$pId]['isSibling']) {
                        $list[$i]['depth'] = $list[$pId]['depth'];
                    }
                } elseif ($s[1] == QUEST_MARK) {
                } elseif ($s[strlen($s) - 2] == TERMINATOR) {
                } elseif ($s[1] == SKIP_MARK) {
                } else {
                    if ($hasCloser) {
                        $depth++;
                    }
                    if (isset($parentIds[$depth])) {
                        unset($parentIds[$depth]);
                    }
                }
            }
        }

        for ($i = $l - 1; $i > -1; $i--) {
            if ($list[$i]['isCloser']) {
                unset($list[$i]);
            }
        }

        // Reindex the matches
        foreach ($list as $key => $value) {
            array_push($result, $value);
        }
        
        return $result;
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
}
