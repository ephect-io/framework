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

    public function getOuterHtml(): array
    {
        $result = [];
        if (count($this->list) === 0) {
            return $result;
        }
        foreach ($this->list as $current) {
            $struct = new ComponentStructure($current);
            $entity = new ComponentEntity($struct);
            $html = $current['text'] . PHP_EOL;
            $html .= $entity->getInnerHTML() . PHP_EOL;
            $html .= $current['closer']['text'] . PHP_EOL;

            $result[] = $html;
        }

        return $result;
    }

    public function getInnerHtml(): array
    {
        $result = [];
        if (count($this->list) === 0) {
            return $result;
        }
        foreach ($this->list as $current) {
            $struct = new ComponentStructure($current);
            $entity = new ComponentEntity($struct);
            $result[] = $entity->getInnerHTML();
        }

        return $result;
    }
}
