<?php

namespace Ephect\Modules\Forms\Events;

interface ComponentFinishedEventInterface
{
    public function invoke(ComponentFinishedEvent $componentFinishedEvent): void;
}