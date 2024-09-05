<?php

namespace Ephect\Framework\Aggregators;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

trait AggregatorTrait
{
    protected array $list = [];

    public function add(string $className): void
    {
        $this->list[] = $className;
    }

    public function aggregate(string $filename): void
    {

        $elementsList = $this->list;
        $existingList = file_exists($filename) ? require $filename : null;

        if (is_array($existingList)) {
            $elementsList = [...$existingList, ...$elementsList];
            $elementsList = array_unique($elementsList);
        }

        $json = json_encode($elementsList);

        $elements = Text::jsonToPhpReturnedArray($json);

        File::safeWrite($filename, $elements);
    }
}