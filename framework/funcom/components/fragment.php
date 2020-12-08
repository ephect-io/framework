<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class Fragment extends AbstractComponent
{
    public function __construct(string $uid)
    {
        CodeRegistry::uncache();

        $this->code = CodeRegistry::read($uid);
        $this->code = urldecode($this->code);
    }

    public function analyse(): void
    {
    }

    public function parse(): void 
    {
        $parser = new Parser($this);
        $parser->doVariables();
        $parser->useVariables();
        $parser->doComponents();
        $parser->doOpenComponents();
        $html = $parser->getHtml();

        $this->code = $html;

    }
}
