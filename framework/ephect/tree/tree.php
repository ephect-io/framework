<?php

namespace Ephect\Tree;

use IteratorAggregate;

class Tree implements TreeInterface
{
    protected $elementList = [];

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
        $this->count = count($this->elementList);
        return $this->count;
    }

    public function getIterator(): TreeIterator
    {
        $tree = new TreeIterator($this->elementList);

        return $tree;
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

    public function insert($object, $index): bool
    {
        $current = [];
        $current[0] = $object;
        $current[1] = $this->elementList[$index];

        array_splice($this->elementList, $index, $current);

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

        $result = false === $result ? null : $result;

        return $result;
    }

    public function clear(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            unset($this->elementList[$i]);
        }
    }

    public function recurse(TreeInterface $tree, callable $callback)
    {
        foreach ($tree as $k => $v) {
            call_user_func($callback, $v);

            if ($v->hasChildren()) {
                $this->recurse($v, $callback);
            }
        }
    }
}
