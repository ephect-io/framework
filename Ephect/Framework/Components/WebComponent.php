<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;

class WebComponent extends AbstractFileComponent
{

    public function makeComponent(string $filename, string &$html): void
    {
        $info = (object) pathinfo($filename);
        $namespace = CONFIG_NAMESPACE;
        $function = $info->filename;

        $html = <<< COMPONENT
        <?php

        namespace $namespace;

        use function Ephect\Hooks\useEffect;

        function $function(\$slot): string
        {
        return (<<< HTML
        <WebComponent>
        $html
        </WebComponent>
        HTML);
        }
        COMPONENT;

        File::safeWrite(COPY_DIR . $filename, $html);

    }

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
        $this->cacheHtml();
    }

}
