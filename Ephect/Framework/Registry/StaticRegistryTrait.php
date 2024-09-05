<?php

namespace Ephect\Framework\Registry;

trait StaticRegistryTrait
{
    abstract public static function reset(): void;

    public static function safeWrite(string $key, $item): bool
    {
        if (false === $result = static::exists($key)) {
            static::write($key, $item);
        }

        return $result;
    }

    public static function exists(string $key): bool
    {
        return static::getInstance()->__exists($key);
    }

    abstract public static function getInstance(): RegistryInterface;

    public static function write(string $key, $item): void
    {
        static::getInstance()->__write($key, $item);
    }

    public static function read($key, $item = null): mixed
    {
        return static::getInstance()->__read($key, $item);
    }

    public static function items(): array
    {
        return static::getInstance()->__items();
    }

    public static function save(bool $asArray = false): bool
    {
        return static::getInstance()->__save($asArray);
    }

    public static function load(bool $asArray = false): bool
    {
        return static::getInstance()->__load($asArray);
    }

    public static function delete(string $key): void
    {
        static::getInstance()->__delete($key);
    }

    public static function setCacheDirectory(string $directory): void
    {
        static::getInstance()->__setCacheDirectory($directory);
    }

    public static function getCacheFilename(bool $asArray = false): string
    {
        return static::getInstance()->__getCacheFilename($asArray);
    }

    public static function getFlatFilename(): string
    {
        return static::getInstance()->__getFlatFilename();
    }

}
