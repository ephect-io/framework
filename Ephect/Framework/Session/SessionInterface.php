<?php

namespace Ephect\Framework\Session;

interface SessionInterface
{
    public function getId(): false|string;

    public function getCookie(): false|string;

    public function isActive(): bool;

    public function start(string $id = '', array $options = []): false|string;

    public function has(string $key): bool;

    public function read(string $key): mixed;

    public function write(string $key, mixed $value): bool;

    public function remove(string $key): void;

    public function clear(): void;
}
