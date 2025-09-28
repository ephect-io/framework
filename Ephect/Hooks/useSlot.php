<?php

namespace Ephect\Hooks;

use JetBrains\PhpStorm\Deprecated;

/**
 * Deprecated
 *
 * @param [type] $callback
 * @param [type] ...$params
 * @return void
 */
#[Deprecated("Useless function", "useEffect", "0.3")]
function useSlot($callback, ...$params): void
{
    call_user_func($callback, ...$params);
}
