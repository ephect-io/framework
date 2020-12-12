<?php

namespace FunCom\Registry;

interface StaticRegistryInterface
{
    static function getInstance(): StaticRegistryInterface;
    static function write(string $key, $item): void;
    static function read($key, $item = null);
    static function items(): array;
    static function cache(): bool;
    static function uncache(): bool;
    static function exists(string $key): bool;
    static function setCacheDirectory(string $directory): void;
}
