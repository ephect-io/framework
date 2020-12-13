<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class PreHtml extends AbstractFileComponent implements FileComponentInterface
{
    public function __construct(string $parentHTML)
    {
        $this->parentHTML = $parentHTML;        
    }
    
    public function analyse(): void
    {
    }

    public function parse(): void 
    {
        $parser = new Parser($this);
        $parser->doMake();
        $parser->doComponents();
        $parser->doOpenComponents();

        $this->code = $parser->getHtml();

    }
}
