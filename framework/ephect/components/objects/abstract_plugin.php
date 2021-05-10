<?php

namespace Ephect\Components;

use Ephect\Components\Generators\ChildrenParser;
use Ephect\Components\Generators\ParserService;
use Ephect\Registry\CodeRegistry;

abstract class AbstractPlugin extends AbstractFileComponent
{
    protected $tag = '';

    public function parse(): void
    {
        CodeRegistry::uncache();

        $parser = new ParserService;

        $parser->doPhpTags($this);
        $this->code = $parser->getHtml();

        $parser->doChildrenDeclaration($this);
        $this->children = $parser->getChildren();

        $parser->doValues($this);
        $this->code = $parser->getHtml();

        $parser->doEchoes($this);
        $this->code = $parser->getHtml();

        $parser->doArrays($this);
        $this->code = $parser->getHtml();

        $parser->doUseEffect($this);
        $this->code = $parser->getHtml();

        $parser->doUseVariables($this);
        $this->code = $parser->getHtml();

        $parser->doNamespace($this);
        $this->code = $parser->getHtml();

        $parser->doFragments($this);
        $this->code = $parser->getHtml();

        $parser->doClosedComponents($this);
        $this->code = $parser->getHtml();

        $parser->doOpenComponents($this);
        $this->code = $parser->getHtml();

        CodeRegistry::cache();

    }

}
