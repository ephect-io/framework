<?php

namespace Ephect\Modules\WebComponent\Module;

use Ephect\Framework\Templates\TemplateMaker;
use Ephect\Modules\Forms\Components\Application\ApplicationComponent;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\WebComponent\Common;

class WebComponent extends ApplicationComponent
{

    public function makeComponent(string $filename, string &$html): void
    {
        $info = (object) pathinfo($filename);
        $namespace = CONFIG_NAMESPACE;
        $function = $info->filename;

        $common =  new Common();

        $componentTextMaker =  new TemplateMaker($common->getModuleSrcDir() . 'Templates' . DIRECTORY_SEPARATOR . 'Component.tpl');
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
