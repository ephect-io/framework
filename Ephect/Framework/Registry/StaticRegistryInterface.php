<?php

namespace Ephect\Framework\Registry;

interface StaticRegistryInterface
{
    public static function getInstance(): RegistryInterface;

    public static function write(string $key, $item): void;

    public static function read($key, $item = null);

    public static function items(): array;

    public static function save(): bool;

    public static function load(): bool;

    public static function delete(string $key): void;

    public static function exists(string $key): bool;

    public static function setCacheDirectory(string $directory): void;

    public static function getCacheFilename(): string;

    public static function getFlatFilename(): string;
}
