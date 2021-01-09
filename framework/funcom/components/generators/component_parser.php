<?php

namespace FunCom\Components\Generators;

use FunCom\Components\ComponentInterface;

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

        // preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        preg_match_all($re, $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

        $result = [];
        // $matches = $matches[0];

        $l = count($matches);
        for ($i = $l - 1; $i > -1; $i--) {
            $matches[$i]['id'] = $i;
            $matches[$i]['component'] = $matches[$i][0][0];
            $matches[$i]['startsAt'] = $matches[$i][0][1];
            $matches[$i]['props'] = $this->doArguments($matches[$i][0][0]);
            // $matches[$i]['hasCloser'] = $matches[$i][1][0] === '/';
            $matches[$i]['hasCloser'] = false;

            if ($matches[$i][1][0] === '/') {
                for ($j = $i - 1; $j > -1; $j--) {
                    if ($matches[$i][2][0] === $matches[$j][2][0] && $matches[$j][1][0] === '') {
                        $matches[$j]['closer'] = [
                            'id' => $i,
                            'parentId' => $j,
                            'component' => $matches[$i][0][0],
                            'startsAt' => $matches[$i][0][1],
                        ];
                        // $matches[$j]['hasCloser'] = true;

                        unset($matches[$i]);
                        break;
                    }
                }
            }

            if (isset($matches[$i])) {
                unset($matches[$i][0]);
                unset($matches[$i][1]);
                unset($matches[$i][2]);
            }
        }

        $list = [];
        foreach ($matches as $key => $value) {
            if(isset($value['closer'])) {
                $value['hasCloser'] = true;
            }
            array_push($list, $value);
        }

        $result = $list;

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
