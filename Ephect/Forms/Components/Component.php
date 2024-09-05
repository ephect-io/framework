<?php

namespace Ephect\Forms\Components;

use Ephect\Forms\Components\Application\ApplicationComponent;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Framework\Utils\File;

class Component extends ApplicationComponent implements FileComponentInterface
{

    public function makeComponent(string $filename, string &$html): void
    {
        $info = (object)pathinfo($filename);
        $namespace = CONFIG_NAMESPACE;
        $function = $info->filename;

        $html = <<< COMPONENT
        <?php

        namespace $namespace;

        function $function(): string
        {
        return (<<< HTML
        $html
        HTML);
        }
        COMPONENT;

        File::safeWrite(COPY_DIR . $filename, $html);
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
