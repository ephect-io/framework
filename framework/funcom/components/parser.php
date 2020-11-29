<?php

namespace FunCom\Components;

class Parser 
{
    private $html;

    public function __construct(string $code)
    {
        $this->html = $code;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function doVariables(): bool
    {
        $result = '';

        $re = '/\{\{ ([a-z]*) \}\}/m';
        $su = '$\1';

        $this->html = preg_replace($re, $su, $this->html);

        $result = $this->html !== null;

        return $result;
    }

    public function doComponents(): bool
    {
        $result = '';

        $re = '/\<([A-Za-z0-9]*)([ ])((\s|[^\/\>].)+)?\/\>/';
        
        preg_match_all($re, $this->html, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);
        
        // TO BE CONTINUED

        $result = $this->html !== null;

        return $result;
    }
}