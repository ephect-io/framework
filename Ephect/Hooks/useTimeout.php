<?php

namespace Ephect\Hooks;

function useTimeout($callback, $ms)
{
  useInterval($callback, $ms, 1);
}
