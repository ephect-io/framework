<?php

namespace Ephect\Registry;

interface AbstractRegistryInterface
{
    function _write(string $key, $item): void;
    function _read($key, $item = null);
    function _items(): array;
    function _cache(): bool;
    function _uncache(): bool;
    function _exists(string $key): bool;
    function _delete(string $key): void;
    function _setCacheDirectory(string $directory): void;
    function _getCacheFilename(): string;
    function _clear(): void;
}
