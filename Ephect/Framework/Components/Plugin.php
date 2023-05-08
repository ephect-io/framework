<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;

class Plugin extends AbstractFileComponent implements FileComponentInterface
{

    public function load(?string $filename = null): bool
    {
        $this->filename = $filename ?: '';

        $this->code = Utils::safeRead(PLUGINS_ROOT . $this->filename);

        [$this->namespace, $this->function, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        if ($this->function === null) {
            [$this->namespace, $this->function, $this->bodyStartsAt] = ElementUtils::getClassDefinition($this->code);
        }
        return $this->code !== null;
    }

    public function makeComponent(string $filename, string &$html): void
    {
    }

    public function analyse(): void
    {
        parent::analyse();

        PluginRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }

}
