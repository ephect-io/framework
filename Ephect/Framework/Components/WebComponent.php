<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;

class WebComponent extends AbstractFileComponent implements WebComponentInterface
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

    static public function htmlToScript($html): string
    {
        $parser = new ComponentParser($html);
        $parser->doComponents('template');
        $list = $parser->getList();
        $struct = new ComponentStructure($list[0]);
        $entity = new ComponentEntity($struct);
        $template = $entity->getInnerHTML();

        $parser->doComponents('script');
        $list = $parser->getList();
        $struct = new ComponentStructure($list[0]);
        $entity = new ComponentEntity($struct);
        $script = $entity->getInnerHTML();

        $parser->doComponents('style');
        $list = $parser->getList();
        $style = '';
        if(count($list) > 0) {
            $struct = new ComponentStructure($list[0]);
            $entity = new ComponentEntity($struct);
            $style = $entity->getInnerHTML();
        }

        $heredoc = <<<HTML
        `
        $style
        $template
        `
        HTML;
        $script = str_replace("document.getElementById('HelloWord').innerHTML", $heredoc, $script);

        return $script;
    }

}
