<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Forms\Application\ApplicationComponent;

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
