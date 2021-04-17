<?php

namespace Ephect\Components;

use Ephect\Registry\ComponentRegistry;

class Component extends AbstractFileComponent
{

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }
    
    public function analyse(): void
    {
        parent::analyse();

        ComponentRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

}
