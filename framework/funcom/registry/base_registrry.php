<?php

namespace FunCom\Registry;

class BaseRegistry extends CustomRegistry implements RegistryInterface
{
    protected static $instance = null;

    protected function __construct()
    {
    }

    public static function getInstance(): RegistryInterface
    {
        if (static::$instance === null) {
            static::$instance = new BaseRegistry();
        }

        return static::$instance;
    }

    public static function write(string $key, $item): void
    {
        static::getInstance()->set($key, $item);
    }

    public static function read($key, $item = null)
    {
        return static::getInstance()->get($key, $item);
    }

    public static function items()
    {
        return static::getInstance()->getAll();
    }

    public static function cache()
    {
        static::getInstance()->save();
    }

}
