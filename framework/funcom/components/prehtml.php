<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class PreHtml extends AbstractFileComponent implements FileComponentInterface
{
    public function __construct(string $preHtml)
    {
        $this->code = $preHtml;        
    }
    
    public function analyse(): void
    {
    }

    public function parse(): void 
    {
        parent::parse();
    }
}
