<?php

namespace Ephect\Registry;

interface StaticRegistryInterface
{
    static function getInstance(): AbstractRegistryInterface;
    static function write(string $key, $item): void;
    static function read($key, $item = null);
    static function items(): array;
    static function cache(): bool;
    static function uncache(): bool;
    static function delete(string $key): void;
    static function exists(string $key): bool;
    static function setCacheDirectory(string $directory): void;
    static function getCacheFilename(): string;
    static function clear(): void;
}
