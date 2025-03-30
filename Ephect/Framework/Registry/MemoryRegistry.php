<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Logger\Logger;

class MemoryRegistry extends AbstractStateRegistry implements StateRegistryInterface
{
    use StaticRegistryTrait;

    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new MemoryRegistry();
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new MemoryRegistry();
        }

        return self::$instance;
    }

    public static function save(bool $asArray = false): bool
    {
        return $asArray;
    }

    public static function load(bool $asArray = false): bool
    {
        return $asArray;
    }

    public static function dump(string $key): void
    {
        Logger::create()->dump('Registry key ' . $key, StateRegistry::item($key));
    }

    public static function item(string|int $item, string|null $value = null): ?array
    {
        return static::getInstance()->__item($item, $value);
    }

    public static function readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed
    {
        return static::getInstance()->__readItem($item, $key, $defaultValue);
    }

    public static function writeItem(string|int $item, ...$params): void
    {
        static::getInstance()->__writeItem($item, ...$params);
    }

    public static function unshift(string|int $item, string|int $key, mixed $value): void
    {
        static::getInstance()->__unshift($item, $key, $value);
    }

    public static function push(string|int $item, string|int $key, mixed $value): void
    {
        static::getInstance()->__push($item, $key, $value);
    }

    public static function keys(string|null $item = null): array
    {
        return static::getInstance()->__keys($item = null);
    }

}
