<?php

namespace Ephect\Modules\Authentication\Repositories;

use Ephect\Modules\Authentication\Entities\User;
use Doctrine\DBAL\Exception;
use Ephect\Framework\DoctrineBridge\Mappers\DataMapper;
use Ephect\Modules\DataAccess\DBAL\Entity;

class UserMapper extends DataMapper
{

    /**
     * @throws Exception
     */
    public function insert(User | Entity &$entity): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO users (email, password, created_at)
            VALUES (:email, :password, :created_at)
        ");

        $stmt->bindValue(":email", $entity->getEmail());
        $stmt->bindValue(":password", $entity->getPassword());
        $stmt->bindValue(":created_at", $entity->getCreatedAt()->format('Y-m-d H:i:s'));

        $stmt->executeStatement();
    }
}
