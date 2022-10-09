<?php

namespace Ephect\Framework\Globals;

function setTimeout($callback, $ms)
{
  setInterval($callback, $ms, 1);
}
