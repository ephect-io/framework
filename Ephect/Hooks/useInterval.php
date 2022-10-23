<?php

namespace Ephect\Hooks;

function useInterval($callback, $ms, $max = 0)
{
  $last = microtime(true);
  $seconds = $ms / 1000;

  register_tick_function(function() use (&$last, $callback, $seconds, $max)
  {
    static $busy = false;
    static $n = 0;

    if ($busy) return;

    $busy = true;

    $now = microtime(true);
    while ($now - $last > $seconds)
    {
      if ($max && $n == $max) break;
      ++$n;

      $last += $seconds;
      $callback();
    }

    $busy = false;
  });
}
