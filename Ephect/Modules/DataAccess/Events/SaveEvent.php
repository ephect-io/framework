<?php

namespace Ephect\Modules\DataAccess\Events;

use Ephect\Framework\Event\Event;
use Ephect\Modules\DataAccess\DBAL\Entity;

class SaveEvent extends Event
{
    public function __construct(private readonly Entity $entity)
    {
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
