<?php

namespace Ephect\Framework\Registry;

interface RegistryInterface
{
    public function __write(string $key, $value): void;

    public function __read($key, $value = null): mixed;

    public function __items(): array;

    public function __save(bool $asArray = false): bool;

    public function __load(bool $asArray = false): bool;

    public function __exists(string $key): bool;

    public function __delete(string $key): void;

    public function __setCacheDirectory(string $directory): void;

    public function __getCacheFilename(bool $asArray = false): string;

    public function __getFlatFilename(): string;
}
