<?php

namespace Ephect\Modules\Http\Event;

interface ResponseEventListenerInterface
{
    public function __invoke(ResponseEvent $event);
}
