<?php

namespace FunCom\Components;

use FunCom\Components\Generators\ChildrenParser;
use FunCom\ElementUtils;
use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\PluginRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

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
