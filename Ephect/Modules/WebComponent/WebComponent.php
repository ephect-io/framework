<?php

namespace Ephect\Modules\WebComponent;

use Ephect\Framework\Templates\TemplateMaker;
use Ephect\Modules\Forms\Components\Application\ApplicationComponent;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

class WebComponent extends ApplicationComponent
{

    public function makeComponent(string $filename, string &$html): void
    {
        $info = (object) pathinfo($filename);
        $namespace = CONFIG_NAMESPACE;
        $function = $info->filename;

        $componentTextMaker =  new TemplateMaker(MODULE_SRC_DIR . 'Templates' . DIRECTORY_SEPARATOR . 'Component.tpl');
        $componentTextMaker->make(['funcNamespace' => $namespace, 'funcName' => $function, 'funcBody' => '', 'html' => $html]);
        $componentTextMaker->save(COPY_DIR . $filename);

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
