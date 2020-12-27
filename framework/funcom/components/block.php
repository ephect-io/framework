<?php

namespace FunCom\Components;

use FunCom\Components\Generators\Parser;
use FunCom\Registry\CodeRegistry;

class Block extends AbstractComponent
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
        $parser->doScalars();
        $parser->useVariables();
        $parser->doComponents();
        $parser->doOpenComponents('Block');
        $html = $parser->getHtml();

        $this->code = $html;

    }
}
