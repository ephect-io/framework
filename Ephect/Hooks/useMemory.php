<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\MemoryRegistry;

/**
 * @param array<array<int|string, int|string>>|null $memory
 * @param string $get
 * @return array
 * @throws \InvalidArgumentException
 */
function useMemory(array|object|null $memory = null, string $get = ''): array
{
    if ($memory !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't assign an object in memory and get an indexed value at once. Pass one or zero argument."
        );
    }

    $setMemory = function (array|object $memory): void {
        MemoryRegistry::writeItem('memory', $memory);
    };


    if ($memory !== null) {
        $setMemory($memory);
    } else {
        $memory = MemoryRegistry::item('memory');

        if ($get !== '' && isset($memory[$get])) {
            $value = (is_array($memory) ? $memory[$get] : $memory?->$get) ;
            return [$value, $setMemory];
        }
    }

    return [$memory, $setMemory];
}
