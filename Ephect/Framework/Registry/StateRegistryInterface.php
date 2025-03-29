<?php

namespace Ephect\Framework\Registry;

interface StateRegistryInterface extends RegistryInterface
{
    public function __saveByMotherUid(string $motherUid, bool $asArray = false): void;

    public function __loadByMotherUid(string $motherUid, bool $asArray = false): void;

    public function __readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed;

    public function __writeItem(string|int $item, ...$params): void;

    public function __unshift(string|int $item, string|int $key, mixed $value): void;

    public function __push(string|int $item, string|int $key, mixed $value): void;

    public function __keys(string|null $item = null): array;

    public function __item(string|int $item, string|null $value = null): array|null;

    public function __ini(string $section, string|null $key = null): string|null;
}
