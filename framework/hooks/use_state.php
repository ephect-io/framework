<?php

namespace Ephect\Hooks;

use Ephect\Registry\Registry;

function useState(?array $state = null): array
{
    $setState = function (array $state) {
        Registry::write('state', $state);
    };

    if ($state !== null) {
        $setState($state);

        $json = json_encode($state);
        $state = json_decode($json);
    }

    if ($state === null) {
        $state = Registry::item('state');

        $json = json_encode($state);
        $state = json_decode($json);
    }



    return [$state, $setState];
}
