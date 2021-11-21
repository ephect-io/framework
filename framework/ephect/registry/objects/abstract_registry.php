<?php

namespace Ephect\Registry;

use Ephect\ElementTrait;
use Ephect\IO\Utils;
abstract class AbstractRegistry implements AbstractRegistryInterface
{
    private $entries = [];
    private $isLoaded = false;
    private $baseDirectory = CACHE_DIR;
    private $cacheFilename = '';
    private $flatFilename = '';

    use ElementTrait;

    public function _items(): array
    {
        return $this->entries;
    }

    public function _write(string $key, $value): void
    {
        if (!isset($this->entries[$key])) {
            $this->entries[$key] = null;
        }
        $this->entries[$key] = $value;
    }

    public function _read($key, $value = null)
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

    public function _delete(string $key): void
    {
        unset($this->entries[$key]);
    }

    public function _exists(string $key): bool
    {
        $result =  isset($this->entries[$key]);

        return $result;
    }

    public function _cache(): bool
    {
        $entries = $this->_items();
        $json = json_encode($entries, JSON_PRETTY_PRINT);
        $registryFilename = $this->_getCacheFileName();
        $len = Utils::safeWrite($registryFilename, $json);

        return $len !== null;
    }

    public function _uncache(): bool
    {
        $this->isLoaded = false;

        $registryFilename = $this->_getCacheFileName();
        $json = Utils::safeRead($registryFilename);
        $this->isLoaded = $json !== null;

        if ($this->isLoaded) {
            $this->entries = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        return $this->isLoaded;
    }

    public function _getFlatFilename(): string
    {
        return $this->flatFilename ?: $this->flatFilename = strtolower(str_replace('\\', '_',  get_class($this))) . '.json';
    }

    public function _getCacheFileName(): string
    {
        if ($this->cacheFilename === '') {
            $this->cacheFilename = $this->baseDirectory . $this->_getFlatFilename();
        }

        return $this->cacheFilename;
    }

    public function _setCacheDirectory(string $directory): void
    {
        $directory = substr($directory, 0, -1) !== DIRECTORY_SEPARATOR ? $directory . DIRECTORY_SEPARATOR : $directory;
        $this->baseDirectory = $directory;
    }

    public function _clear(): void
    {
        $this->entries = [];
    }
}
