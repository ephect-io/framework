<?php

namespace Ephect\Hooks;

use JetBrains\PhpStorm\Deprecated;

/**
 * Deprecated
 *
 * @param callable $callback
 * @param mixed ...$params
 * @return void
 */
function useSlot(callable $callback, mixed ...$params): void
{
    call_user_func($callback, ...$params);
}
