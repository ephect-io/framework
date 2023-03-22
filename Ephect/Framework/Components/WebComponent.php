<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\Generators\Parser;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;

class WebComponent extends AbstractFileComponent
{

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

    static public function split($html): array
    {
        $parser = new ComponentParser($html);
        $parser->doComponents('template');
        $list = $parser->getList();
        $template = $list[0]['closer']['contents']['text'];
        $parser->doComponents('script');
        $list = $parser->getList();
        $script = $list[0]['closer']['contents']['text'];

        return [$template, $script];
    }

}
