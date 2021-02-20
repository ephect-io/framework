<?php

namespace Ephect\Hooks;

use Ephect\Registry\Registry;

function useState($item, $key)
{
    return Registry::read($item, $key);
}

function setState($item, ...$params)
{
    return Registry::write($item, ...$params);
}