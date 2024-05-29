<?php

namespace Ephect\Framework\Registry;

trait StaticRegistryTrait
{
    public abstract static function reset(): void;

    public static function safeWrite(string $key, $item): bool
    {
        if (false === $result = static::exists($key)) {
            static::write($key, $item);
        }

        return $result;
    }

    public static function exists(string $key): bool
    {
        return static::getInstance()->_exists($key);
    }

    public abstract static function getInstance(): RegistryInterface;

    public static function write(string $key, $item): void
    {
        static::getInstance()->_write($key, $item);
    }

    public static function read($key, $item = null)
    {
        return static::getInstance()->_read($key, $item);
    }

    public static function items(): array
    {
        return static::getInstance()->_items();
    }

    public static function cache(bool $asArray = false): bool
    {
        return static::getInstance()->_cache($asArray);
    }

    public static function uncache(bool $asArray = false): bool
    {
        return static::getInstance()->_uncache($asArray);
    }

    public static function delete(string $key): void
    {
        static::getInstance()->_delete($key);
    }

    public static function setCacheDirectory(string $directory): void
    {
        static::getInstance()->_setCacheDirectory($directory);
    }

    public static function getCacheFilename(bool $asArray = false): string
    {
        return static::getInstance()->_getCacheFilename($asArray);
    }

    public static function getFlatFilename(): string
    {
        return static::getInstance()->_getFlatFilename();
    }

}
