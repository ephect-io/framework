<?php

namespace Ephect\Modules\DataAccess\Events;

use Ephect\Framework\Event\Event;

class ConnectionOpenerEvent extends Event
{
    public function __construct(
        private readonly object $arguments
    ) {
    }

    public function getArguments(): object
    {
        return $this->arguments;
    }
}
