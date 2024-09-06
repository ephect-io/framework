<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Modules\Forms\Components\Application\ApplicationComponent;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;

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
