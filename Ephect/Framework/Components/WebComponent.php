<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;

class WebComponent extends AbstractFileComponent
{

    public function analyse(): void
    {
        parent::analyse();

        WebComponentRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
        ComponentRegistry::cache();

    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheJavascript();
    }

}
