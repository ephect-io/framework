<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Logger\Logger;

class StateRegistry extends AbstractStateRegistry implements RegistryInterface
{
    use StaticRegistryTrait;

    private static ?StateRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new StateRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function saveByMotherUid(string $motherUid, bool $asArray = false): void
    {
        static::getInstance()->__saveByMotherUid($motherUid, $asArray);
    }

    public static function getInstance(): StateRegistry
    {
        if (self::$instance === null) {
            self::$instance = new StateRegistry();
        }

        return self::$instance;
    }

    public static function loadByMotherUid(string $motherUid, bool $asArray = false): void
    {
        static::getInstance()->__loadByMotherUid($motherUid, $asArray);
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

    public static function ini(string $section, string|null $key = null): string|null
    {
        return static::getInstance()->__ini($section, $key);
    }
}
