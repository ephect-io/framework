<?php

namespace Ephect\Framework\Aggregators;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

trait ComponentParserMiddlewareAggregatorTrait
{
    use AggregatorTrait;

    protected function aggregateComponentParserMiddlewares()
    {
        $middlewaresList = $this->list;
        $existingMiddlewaresList = [];
        if (file_exists(CACHE_DIR . 'componentsParserMiddlewares.php')) {
            $existingMiddlewaresList = require CACHE_DIR . 'componentsParserMiddlewares.php';
        }

        if (is_array($existingMiddlewaresList)) {
            $middlewaresList = array_merge($existingMiddlewaresList, $this->list);
            $middlewaresList = array_unique($middlewaresList);
        }

        $json = json_encode($middlewaresList);

        $middlewares = Text::jsonToPhpReturnedArray($json);

        File::safeWrite(CACHE_DIR . 'componentsParserMiddlewares.php', $middlewares);
    }
}