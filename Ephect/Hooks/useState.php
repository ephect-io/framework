<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\StateRegistry;

function useState(array|object $state = null): array
{
    $setState = function (array|object $state) {
        StateRegistry::writeItem('state', $state);
    };

    if ($state !== null) {
        $setState($state);
    }

    if ($state === null) {
        $state = StateRegistry::item('state');
    }

    return [$state, $setState];
}
