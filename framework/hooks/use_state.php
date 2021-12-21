<?php

namespace Ephect\Hooks;

use Ephect\Registry\StateRegistry;

function useState(array|object $state = null): array
{
    $setState = function (array|object $state) {
        StateRegistry::write('state', $state);
    };

    if ($state !== null) {
        $setState($state);

        $json = json_encode($state);
        $state = json_decode($json);
    }

    if ($state === null) {
        $state = StateRegistry::item('state');

        $json = json_encode($state);
        $state = json_decode($json);
    }

    return [$state, $setState];
}
