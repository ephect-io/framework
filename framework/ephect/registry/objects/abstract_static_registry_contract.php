<?php

namespace Ephect\Registry;

use Ephect\ElementTrait;
use Ephect\IO\Utils;

abstract class AbstractStaticRegistryContract extends AbstractRegistryContract implements AbstractRegistryInterface {    
    private $entries = [];
    private $isLoaded = false;
    private $baseDirectory = CACHE_DIR;
    private $cacheFilename = '';

    use ElementTrait;

    protected function __construct()
    {
    }

    abstract public static function getInstance(): AbstractRegistryInterface;

    protected function _items(): array
    {
        return $this->entries;
    }

    protected function _write(string $key, $value): void
    {
        if (!isset($this->entries[$key])) {
            $this->entries[$key] = null;
        }
        $this->entries[$key] = $value;
    }

    protected function _read($key, $value = null)
    {
        if (!isset($this->entries[$key])) {
            return null;
        }

        if ($value === null) {
            return $this->entries[$key];
        }

        if (!isset($this->entries[$key][$value])) {
            return null;
        }

        return $this->entries[$key][$value];
    }

    protected function _delete(string $key): void
    {
        unset($this->entries[$key]);
    }

    protected function _exists(string $key): bool
    {
        $result =  isset($this->entries[$key]);

        return $result;
    }

    protected function _cache(): bool
    {
        $entries = $this->_items();
        $json = json_encode($entries, JSON_PRETTY_PRINT);
        $registryFilename = $this->_getCacheFileName();
        $len = Utils::safeWrite($registryFilename, $json);

        return $len !== null;
    }

    protected function _uncache(): bool
    {
        if ($this->isLoaded) {
            return false;
        }

        $registryFilename = $this->_getCacheFileName();
        $json = Utils::safeRead($registryFilename);
        $this->isLoaded = $json !== null;

        if($this->isLoaded) {
            $this->entries = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        return $this->isLoaded;
    }

    protected function _getCacheFileName(): string
    {
        if($this->cacheFilename === '')
        {
            $fileName = str_replace('\\', '_',  get_class($this));
            $this->cacheFilename =$this->baseDirectory . strtolower($fileName) . '.json';
             
        }

        return $this->cacheFilename;
    }

    protected function _setCacheDirectory(string $directory): void
    {
        $this->baseDirectory = $directory;
    }

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

    public static function clear(): void
    {
        static::getInstance()->_clear();
    }
}
