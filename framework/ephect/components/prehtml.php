<?php

namespace Ephect\Components;

use Ephect\Registry\CodeRegistry;

class PreHtml extends AbstractComponent implements ComponentInterface
{
    public function __construct(string $preHtml)
    {
        $this->code = $preHtml;        
    }
    
    public function analyse(): void
    {
        parent::analyse();
    }

    public function parse(): void 
    {
        parent::parse();
    }
}
