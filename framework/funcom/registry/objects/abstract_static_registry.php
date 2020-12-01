<?php

namespace FunCom\Registry;

abstract class AbstractStaticRegistry extends AbstractRegistry implements StaticRegistryInterface
{
    protected function __construct()
    {
    }

    public abstract static function getInstance(): StaticRegistryInterface;

    public static function write(string $key, $item): void
    {
        static::getInstance()->setEntry($key, $item);
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
        return static::getInstance()->getEntry($key, $item);
    }

    public static function items(): array
    {
        return static::getInstance()->getAll();
    }

    public static function cache(): void
    {
        static::getInstance()->save();
    }

    public static function uncache(): void
    {
        static::getInstance()->load();
    }

    public static function exists(string $key): bool
    {
        return static::getInstance()->entryExists($key);
    }
}
