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
        
        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);
        

        foreach($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[3]) ? $match[3] : '';

            $componentRender = "<?php FunCom\Components\View::render('$componentName', '$componentArgs'); ?>";
            
            $this->html = str_replace($component, $componentRender, $this->html);

        }
        // TO BE CONTINUED

        $result = $this->html !== null;

        return $result;
    }
}