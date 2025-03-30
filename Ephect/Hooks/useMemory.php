<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\MemoryRegistry;

/**
 * @param array<array<int|string, int|string>>|null $memory
 * @param string $get
 * @return array<mixed, \Closure>
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

        $json = json_encode($memory);
        $memory = json_decode($json);
    } else {
        $memory = MemoryRegistry::item('memory');

        $json = json_encode($memory);
        $memory = json_decode($json);

        if ($get !== '') {
            return [$memory->$get ?? null, $setMemory];
        }
    }

    return [$memory, $setMemory];
}
