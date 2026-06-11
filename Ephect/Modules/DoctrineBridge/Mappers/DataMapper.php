<?php

namespace Ephect\Modules\DoctrineBridge\Mappers;

use Ephect\Framework\Event\EventDispatcher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Ephect\Modules\DataAccess\DBAL\Entity;
use Ephect\Modules\DataAccess\Events\SaveEvent;

abstract class DataMapper
{
    public function __construct(
        protected Connection      $connection,
        protected EventDispatcher $dispatcher,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function save(Entity $entity): void
    {
        $this->insert($entity);
        $id = $this->connection->lastInsertId();
        $entity->setId($id);
        $this->dispatcher->dispatch(new SaveEvent($entity));
    }

    abstract public function insert(Entity &$entity): void;
}
