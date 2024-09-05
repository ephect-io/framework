<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\StateRegistry;

/**
 * @param array|object|null $store
 * @return array
 */
function useStore(array|object|null $store = null, string $get = ''): array
{
    if ($store !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't assign a state and get an indexed value at once. Pass one or zero argument."
        );
    }

    $setStore = function (array|object $store): void {
        StateRegistry::writeItem('store', $store);
    };

    if ($store !== null) {
        $setStore($store);

        $json = json_encode($store);
        $store = json_decode($json);
    } else {
        $store = StateRegistry::item('store');

        $json = json_encode($store);
        $store = json_decode($json);

        if ($get !== '') {
            return [$store->$get ?? null, $setStore];
        }
    }

    return [$store, $setStore];
}
