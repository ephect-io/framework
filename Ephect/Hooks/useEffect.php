<?php

namespace Ephect\Hooks;

function useEffect($callback, ...$params): void
{
    call_user_func($callback, ...$params);
}
