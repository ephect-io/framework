<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\MemoryRegistry;

/**
 * @param array<array<int|string, int|string>>|null $memory
 * @param string $get
 * @return array
 * @throws \InvalidArgumentException
 */
function useContainer(object|null $class = null, string $get = ''): array
{
    if ($class !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't assign an object in memory and get an indexed value at once. Pass one or zero argument."
        );
    }

    $setContainer = function (object $class): void {
        MemoryRegistry::writeItem('container', $class);
    };


    if ($class !== null) {
        $setContainer($class);
    } else {
        $container = MemoryRegistry::item('container');

        if ($get !== '' && isset($container[$get])) {
            $value = (is_array($container) ? $container[$get] : $container?->$get) ;
            return [$value, $setContainer];
        }
    }

    return [$container, $setContainer];
}
