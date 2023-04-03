<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\IO\Utils;
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

        function $function(\$props) {
        return (<<< HTML
        <WebComponent>
        $html
        </WebComponent>
        HTML);
        }
        COMPONENT;

        Utils::safeWrite(COPY_DIR . $filename, $html);

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
