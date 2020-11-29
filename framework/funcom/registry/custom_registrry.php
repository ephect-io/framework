<?php

namespace FunCom\Registry;

use FunCom\IO\Utils;

abstract class CustomRegistry
{
    private $items = [];
    private $isLoaded = false;

    protected function getName(): string
    {
        $parts = explode('\\', get_class($this));
        $className = array_pop($parts);

        return $className;
    }
    
    public function getAll()
    {
        return $this->items;
    }

    public function set(string $key, $item): void
    {
        if (!isset($this->items[$key])) {
            $this->items[$key] = null;
        }
        $this->items[$key] = $item;
    }

    public function get($key, $item = null)
    {
        if (!isset($this->items[$key])) {
            throw new \Exception("No key like $key has been found");
        }

        if ($item === null) {
            return $this->items[$key];
        }

        if (!isset($this->items[$key][$item])) {
            throw new \Exception("No item like $item has been found under key $key");
        }

        return $this->items[$key][$item];
    }


    public function save(): void
    {
        $items = $this->getAll();

        $json = json_encode($items, JSON_PRETTY_PRINT);

        $registryFilename = $this->getCacheFileName();

        Utils::safeWrite($registryFilename, $json);
    }

    public function load(): void
    {
        if($this->isLoaded) {
            return;
        }

        $registryFilename = $this->getCacheFileName();

        $json = Utils::safeRead($registryFilename);

        $this->items = json_decode($json, JSON_OBJECT_AS_ARRAY);

        $this->isLoaded = true;
    }

    public function getCacheFileName() : string
    {
        $className = $this->getName();
        $result = CACHE_DIR . strtolower($className) . '.json';

        return $result;

    }
}
