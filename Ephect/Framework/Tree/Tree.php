<?php

namespace Ephect\Framework\Tree;

class Tree implements TreeInterface
{
    protected array $elementList = [];

    public function __construct(array $list)
    {
        $this->elementList = $list;
    }

    public function hasChildren(): bool 
    {
        return $this->count() > 0;
    }

    public function count(): int
    {
        return count($this->elementList);
    }

    public function getIterator(): TreeIterator
    {
        return new TreeIterator($this->elementList);
    }

    public function items(?int $index = null)
    {
        if ($index === null) {
            return $this->elementList;
        }
        return $this->elementList[$index];
    }

    public function add($object): int
    {
        $index = count($this->elementList);
        $this->elementList[$index] = $object;

        return $index;
    }

    public function addArray($array): int
    { 
        $result = 0;
        foreach($array as $item) {
            $result = $this->add($item);
        }

        return $result;
    }

    public function insert($object, $index): bool
    {
        $current = [];
        $current[0] = $object;
        $current[1] = $this->elementList[$index];

        array_splice($this->elementList, $index, null, $current);

        return true;
    }

    public function update($object, $index): bool
    {
        $this->elementList[$index] = $object;

        return true;
    }

    public function removeAt($index): bool
    {
        unset($this->elementList[$index]);

        return true;
    }

    public function find($object): ?int
    {
        $result = array_search($object, $this->elementList, true);

        return false === $result ? null : $result;
    }

    public function clear(): void
    {
        $c = count($this->elementList);
        for ($i = 0; $i < $c; $i++) {
            unset($this->elementList[$i]);
        }

        $this->elementList = [];
    }

    public function forEach(callable $callback, TreeInterface $tree): void
    {
        foreach ($tree as $key => $item) {
            call_user_func($callback, $item, $key);

            if ($item->hasChildren()) {
                $this->forEach($callback, $item, $key);
            }
        }
    }

    public function forEachRecursive(callable $callback, TreeInterface $tree): void
    {
        foreach ($tree as $key => $item) {
            if ($item->hasChildren()) {
                $this->forEach($callback, $item, $key);
            }

            call_user_func($callback, $item, $key);
        }
    }
}
