<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\StateRegistry;

function useStateObject(array|object|null $state = null): array
{
    $setState = function (array|object $state) {
        StateRegistry::writeItem('stateObject', $state);
    };

    if ($state !== null) {
        $setState($state);

        $json = json_encode($state);
        $state = json_decode($json);
    }

    if ($state === null) {
        $state = StateRegistry::item('stateObject');

        $json = json_encode($state);
        $state = json_decode($json);
    }

    return [$state, $setState];
}
