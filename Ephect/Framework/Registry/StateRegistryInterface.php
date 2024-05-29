<?php

namespace Ephect\Framework\Registry;


interface StateRegistryInterface extends RegistryInterface
{
    public function _cacheByMotherUid(string $motherUid, bool $asArray = false): void;

    public function _readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed;

    public function _writeItem(string|int $item, ...$params): void;

    public function _unshift(string|int $item, string|int $key, mixed $value): void;

    public function _push(string|int $item, string|int $key, mixed $value): void;

    public function _keys(string|null $item = null): array;

    public function _item(string|int $item, string|null $value = null): array|null;

    public function _ini(string $section, string|null $key = null): string|null;
}
