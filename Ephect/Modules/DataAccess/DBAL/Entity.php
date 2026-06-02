<?php

namespace Ephect\Modules\DataAccess\DBAL;

abstract class Entity implements EntityInterface
{
    protected int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
