<?php

namespace Ephect\Components;

use Ephect\Registry\ComponentRegistry;

class Component extends AbstractFileComponent
{

    public function __construct(string $uid = '', string $motherUID = '')
    {
        $this->uid = $uid;
        $this->motherUID = ($motherUID === '') ? $uid : $motherUID;
        $this->getUID();
    }

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
