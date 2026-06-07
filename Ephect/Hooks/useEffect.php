<?php

namespace Ephect\Hooks;

function useEffect(callable$callback, mixed ...$params): void
{
    call_user_func($callback, ...$params);
}
