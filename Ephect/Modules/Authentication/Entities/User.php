<?php

namespace Ephect\Modules\Authentication\Entities;

use Ephect\Modules\Authentication\Components\AuthenticationInterface;
use DateTimeImmutable;
use Ephect\Modules\DataAccess\DBAL\Entity;

class User extends Entity implements AuthenticationInterface
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public function getAuthId(): int | string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public static function create(string $email, string $password): self
    {
        return new self(
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT),
            createdAt: new DateTimeImmutable(),
        );
    }

}
