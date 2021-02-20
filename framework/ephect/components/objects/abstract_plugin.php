<?php

namespace Ephect\Components;

use Ephect\Components\Generators\ChildrenParser;
use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\PluginRegistry;
use Ephect\Registry\UseRegistry;
use Ephect\Registry\ViewRegistry;

abstract class AbstractPlugin extends AbstractFileComponent
{
    protected $tag = '';

    public function analyse(): void
    {
        parent::analyse();
    }

    
    public function parse(): void
    {
        $parser = new ChildrenParser($this);

        $parser->doUncache();

        $this->children = $parser->doChildrenDeclaration();
        $parser->doScalars();
        $parser->doArrays();
        $parser->useVariables();
        $parser->normalizeNamespace();
        $parser->doComponents();
        $this->componentList = $parser->doOpenComponents($this->tag);
        $html = $parser->getHtml();

        $parser->doCache();

        $this->code = $html;
    }

}
