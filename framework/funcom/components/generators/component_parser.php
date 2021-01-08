<?php

namespace FunCom\Components\Generators;

use FunCom\Components\ComponentInterface;

class ComponentParser
{

    protected  $html = '';
    protected  $view = null;
    protected  $useVariables = [];
    protected  $parentHTML = '';
    protected  $maker = null;

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
        for($i = $l - 1; $i > -1; $i--) {
            $match = $matches[$i];

            if($match[1][0] === '/') {

                $needle = $match[2][0];
                for($j = $i - 1; $j > -1; $j--) {
                    $sibling = $matches[$j][2][0];
                    $closing = $matches[$j][1][0] === '';

                    if($needle === $sibling && $closing) {
                        $matches[$j][3] = $match;
                        unset($matches[$i]);

                    break;
                    }
                }
            }
        }

        $result = $matches;

        return $result;
    }
}