<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\Components\ComponentEntity;
use Ephect\Framework\Components\ComponentStructure;

class RawHtmlParser implements RawHtmlParserInterface
{
    private array $list;

    public function __construct(private string $html)
    {
    }

    public function doTag(string $tag): void
    {
        $parser = new ComponentParser($this->html);
        $parser->doComponents($tag);
        $this->list = $parser->getList();
        unset($parser);
    }

    public function getInnerHtml(): string
    {
        $result = '';
        if(count($this->list) === 0) {
            return $result;
        } 
        $struct = new ComponentStructure($this->list[0]);
        $entity = new ComponentEntity($struct);
        $result = $entity->getInnerHTML();

        return $result;
    }
}
