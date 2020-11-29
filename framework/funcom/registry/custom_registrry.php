<?php

namespace FunCom\Registry;

abstract class CustomRegistry
{
    private $items = [];

    protected function getName(): string
    {
        $className = array_pop(explode('\\', get_class($this)));

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
        $views = $this->getAll();

        $json = json_encode($views, JSON_PRETTY_PRINT);

        $className = $this->getName();
        $className = str_replace('\\', '', $className);

        $registry = CACHE_DIR . strtolower($className) . '.json';

        file_put_contents($registry, $json);
    }
}
