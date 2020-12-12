<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class Fragment extends AbstractComponent
{
    public function __construct(string $uid, string $parentHTML)
    {
        CodeRegistry::uncache();

        $this->parentHTML = $parentHTML;        

        $this->code = CodeRegistry::read($uid);
        $this->code = urldecode($this->code);
    }

    public function analyse(): void
    {
    }

    public function parse(): void 
    {
        $parser = new Parser($this);
        $parser->doScalars();
        $parser->useVariables();
        $parser->doComponents();
        $parser->doOpenComponents();
        $html = $parser->getHtml();

        $this->code = $html;

    }
}
