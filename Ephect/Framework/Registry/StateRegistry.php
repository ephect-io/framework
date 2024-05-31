<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Logger\Logger;

class StateRegistry extends AbstractStateRegistry implements StateRegistryInterface
{

    use StaticRegistryTrait;

    private static ? StateRegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new StateRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): StateRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new StateRegistry;
        }

        return self::$instance;
    }

    public static function saveByMotherUid(string $motherUid, bool $asArray = false): void
    {
        static::getInstance()->_saveByMotherUid($motherUid, $asArray);
    }

    public static function loadByMotherUid(string $motherUid, bool $asArray = false): void
    {
        static::getInstance()->_loadByMotherUid($motherUid, $asArray);
    }

    public static function dump(string $key): void
    {
        Logger::create()->dump('Registry key ' . $key, StateRegistry::item($key));
    }

    public static function readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed
    {
        return static::getInstance()->_readItem($item, $key, $defaultValue);
    }

    public static function writeItem(string|int $item, ...$params): void
    {
        static::getInstance()->_writeItem($item, ...$params);
    }

    public static function unshift(string|int $item, string|int $key, mixed $value): void
    {
        static::getInstance()->_unshift($item, $key, $value);
    }

    public static function push(string|int $item, string|int $key, mixed $value): void
    {
        static::getInstance()->_push($item, $key, $value);
    }

    public static function keys(string|null $item = null): array
    {
        return static::getInstance()->_keys($item = null);
    }

    public static function item(string|int $item, string|null $value = null): ?array
    {
        return static::getInstance()->_item($item, $value);
    }

    public static function ini(string $section, string|null $key = null): string|null
    {
        return static::getInstance()->_ini($section, $key);
    }

}
