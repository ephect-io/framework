<?php

namespace Ephect\Hooks;

function useSlot($callback, ...$params)
{
    call_user_func($callback, ...$params);
}
