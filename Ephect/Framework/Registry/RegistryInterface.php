<?php

namespace Ephect\Framework\Registry;

interface RegistryInterface
{
    function _write(string $key, $value): void;

    function _read($key, $value = null);

    function _items(): array;

    function _save(bool $asArray = false): bool;

    function _load(bool $asArray = false): bool;

    function _exists(string $key): bool;

    function _delete(string $key): void;

    function _setCacheDirectory(string $directory): void;

    function _getCacheFilename(bool $asArray = false): string;

    function _getFlatFilename(): string;
}
