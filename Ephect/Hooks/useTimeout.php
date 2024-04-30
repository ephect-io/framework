<?php

namespace Ephect\Hooks;

function useTimeout($callback, $ms): void
{
    useInterval($callback, $ms, 1);
}
