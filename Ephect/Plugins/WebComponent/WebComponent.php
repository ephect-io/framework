<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\Components\AbstractFileComponent;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\Utils\File;

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

        #[WebComponentZeroConf]
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

        ComponentRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
        ComponentRegistry::save();
    }

    public function parse(): void
    {
        parent::parse();
        $this->cacheHtml();
    }

}
