<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\Element;

abstract class AbstractCommandLib extends Element
{
    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
