<?php

namespace FunCom\Hooks;

function useEffect($callback, ...$params)
{
    call_user_func($callback, ...$params);
}
