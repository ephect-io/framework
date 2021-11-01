<?php

namespace Ephect\Registry;

abstract class AbstractStaticRegistry extends AbstractRegistry implements StaticRegistryInterface, AbstractRegistryInterface
{    
    public abstract static function getInstance(): AbstractRegistryInterface;

    public static function write(string $key, $item): void
    {
        static::getInstance()->_write($key, $item);
    }

    public static function safeWrite(string $key, $item): bool
    {
        if (false === $result = static::exists($key)) {
            static::write($key, $item);
        }

        return $result;
    }

    public static function read($key, $item = null)
    {
        return static::getInstance()->_read($key, $item);
    }

    public static function items(): array
    {
        return static::getInstance()->_items();
    }

    public static function cache(): bool
    {
        return static::getInstance()->_cache();
    }

    public static function uncache(): bool
    {
        return static::getInstance()->_uncache();
    }

    public static function delete(string $key): void
    {
        static::getInstance()->_delete($key);
    }
    
    public static function exists(string $key): bool
    {
        return static::getInstance()->_exists($key);
    }

    public static function setCacheDirectory(string $directory): void
    {
        static::getInstance()->_setCacheDirectory($directory);
    }

    public static function getCacheFilename(): string
    {
        return static::getInstance()->_getCacheFilename();
    }
    
    public static function getFlatFilename(): string 
    {
        return static::getInstance()->_getFlatFilename();
    }

    public static function clear(): void
    {
        static::getInstance()->_clear();
    }
}
