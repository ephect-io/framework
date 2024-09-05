<?php

namespace Ephect\Forms\Components;

use Ephect\Forms\Components\Application\ApplicationComponent;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Forms\Registry\PluginRegistry;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Utils\File;

class Plugin extends Component implements FileComponentInterface
{

    public function makeComponent(string $filename, string &$html): void
    {
    }

    public function analyse(): void
    {
        ApplicationComponent::analyse();

        PluginRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

}
