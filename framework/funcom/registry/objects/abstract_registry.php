<?php

namespace FunCom\Registry;

use FunCom\IO\Utils;

abstract class AbstractRegistry
{
    private $entries = [];
    private $isLoaded = false;

    protected function getName(): string
    {
        $parts = explode('\\', get_class($this));
        $className = array_pop($parts);

        return $className;
    }

    public function getAll()
    {
        return $this->entries;
    }

    public function setEntry(string $key, $value): void
    {
        if (!isset($this->entries[$key])) {
            $this->entries[$key] = null;
        }
        $this->entries[$key] = $value;
    }

    public function getEntry($key, $value = null)
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

    public function entryExists(string $key): bool
    {
        $result =  isset($this->entries[$key]);

        return $result;
    }

    public function save(): void
    {
        $entries = $this->getAll();
        $json = json_encode($entries, JSON_PRETTY_PRINT);
        $registryFilename = $this->getCacheFileName();
        Utils::safeWrite($registryFilename, $json);
    }

    public function load(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $registryFilename = $this->getCacheFileName();
        $json = Utils::safeRead($registryFilename);
        $this->entries = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $this->isLoaded = true;
    }

    public function getCacheFileName(): string
    {
        $fileName = $this->getName();
        $result = CACHE_DIR . strtolower($fileName) . '.json';

        return $result;
    }
}
