<?php

namespace Ephect\Framework\Tree;

use Closure;

trait TreeTrait
{
    protected array $elementList = [];

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

    public function addArray($array): int
    {
        $result = 0;
        foreach ($array as $item) {
            $result = $this->add($item);
        }

        return $result;
    }

    public function add($object): int
    {
        $index = count($this->elementList);
        $this->elementList[$index] = $object;

        return $index;
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

    public function forEachRecursive(callable $callback, TreeInterface $tree, Closure|null $breakOn = null): void
    {
        foreach ($tree as $key => $item) {
            if ($item->hasChildren()) {
                $this->forEachRecursive($callback, $item, $breakOn);
            }

            call_user_func($callback, $item, $key);
            if ($breakOn != null && $breakOn()) {
                break;
            }
        }
    }

    public function hasChildren(): bool
    {
        return $this->count() > 0;
    }

    public function count(): int
    {
        return count($this->elementList);
    }

    public function forEach(callable $callback, TreeInterface $tree, Closure|null $breakOn = null): void
    {
        foreach ($tree as $key => $item) {
            call_user_func($callback, $item, $key);
            if ($breakOn != null && $breakOn()) {
                break;
            }
            if ($item->hasChildren()) {
                $this->forEach($callback, $item, $breakOn);
            }
        }
    }
}
