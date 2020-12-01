<?php

namespace FunCom\Registry;

interface StaticRegistryInterface
{
    static function getInstance(): StaticRegistryInterface;
    static function write(string $key, $item): void;
    static function read($key, $item = null);
    static function items(): array;
    static function cache(): void;
    static function uncache(): void;
    static function exists(string $key): bool;
}
