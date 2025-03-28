<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\StateRegistry;

/**
 * @param array|object|null $state
 * @return array
 */
function useStore(array|object|null $state = null, string $get = ''): array
{
    if ($state !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't assign a state and get an indexed value at once. Pass one or zero argument."
        );
    }

    $setState = function (array|object $state): void {
        StateRegistry::writeItem('store', $state);
    };

    if ($state !== null) {
        $setState($state);

        $json = json_encode($state);
        $state = json_decode($json);
    } else {
        $state = StateRegistry::item('store');

        $json = json_encode($state);
        $state = json_decode($json);

        if ($get !== '') {
            return [$state[$get] ?? null, $setState];
        }
    }

    return [$state, $setState];
}
