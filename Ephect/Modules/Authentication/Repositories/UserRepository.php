<?php

namespace Ephect\Modules\Authentication\Repositories;

use Ephect\Modules\Authentication\Components\AuthenticationInterface;
use Ephect\Modules\Authentication\Entities\User;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserRepository implements AuthenticationRepositoryInterface
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @throws Exception
     */
    public function findByEmail(string $email): ?AuthenticationInterface
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('id', 'email', 'password', 'created_at')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAllAssociative();

        if (!isset($row[0])) {
            return null;
        }

        $obj = (object) $row[0];

        $user = new User(
            email: $obj->email,
            password: $obj->password,
            createdAt: new DateTimeImmutable($obj->created_at),
            id: $obj->id
        );

        return $user;
    }
}
