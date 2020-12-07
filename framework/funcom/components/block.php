<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class Block extends AbstractComponent
{
    public function __construct(string $uid)
    {
        $this->code = CodeRegistry::read($uid);
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
        $parser->doOpenComponents('Block');
        $html = $parser->getHtml();

        $this->code = $html;

    }
}
