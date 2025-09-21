<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\StateRegistry;

/**
 * @param array<array<int|string, int|string>>|null $state
 * @param string $get
 * @return array<mixed, \Closure>
 * @throws \InvalidArgumentException
 */
function useState(?array $state = null, string $get = ''): array
{
    if ($state !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't assign a state and get an indexed value at once. Pass one or zero argument."
        );
    }

    $setState = function (array $state): void {
        StateRegistry::write('state', $state);
    };

    if ($state !== null) {
        $setState($state);

        $json = json_encode($state);
        $state = json_decode($json, true);
    } else {
        $state = StateRegistry::item('state');

        $json = json_encode($state);
        $state = json_decode($json, true);

        if ($get !== '') {
            return [$state[$get] ?? null, $setState];
        }
    }

    return [$state, $setState];
}
