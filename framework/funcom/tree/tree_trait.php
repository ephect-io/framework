<?php

namespace Ephect\Tree;

use Ephect\ElementTrait;
use Ephect\Tree\TreeInterface;

trait TreeTrait
{

    use ElementTrait;

    protected $innerList = [];
    protected $count = 0;
    protected $innerNode = null;
    protected $hasChildren = false;
    protected $index = 0;

    public function bind(array $collection): void
    {
        $this->innerList = $collection;
        $this->count = count($this->innerList);
    }

    public function count(): int
    {
        $this->count = count($this->innerList);
        return $this->count;
    }

    public function items(?int $index = null)
    {
        if ($index === null) {
            return $this->innerList;
        }
        return $this->innerList[$index];
    }

    public function add($object): int
    {
        $index = count($this->innerList);
        $this->innerList[$index] = $object;

        return $index;
    }

    public function insert($object, $index): bool
    {
        $current = [];
        $current[0] = $object;
        $current[1] = $this->innerList[$index];

        array_splice($this->innerList, $index, $current);

        return true;
    }

    public function update($object, $index): bool
    {
        $this->innerList[$index] = $object;

        return true;
    }

    public function removeAt($index): bool
    {
        unset($this->innerList[$index]);

        return true;
    }

    public function find($object): ?int
    {
        $result = array_search($object, $this->innerList, true);

        $result = false === $result ? null : $result;

        return $result;
    }

    public function clear(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            unset($this->innerList[$i]);
        }
    }

    public function current() 
    {
        if(!$this->valid()) {
            return null;
        }
        return $this->innerList[$this->index];
    }

    public function key(): int
    {
        return $this->index;
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function valid(): bool
    {
        return isset($this->innerList[$this->index]);

    }

    public function node(): ?TreeInterface
    {
        if ($this->innerNode === null) {
            $this->innerNode = new Tree();
        }
        return $this->innerNode;
    }

    public function hasNode(): bool
    {
        if ($this->hasChildren === false) {
            $this->hasChildren = $this->innerNode !== null && $this->innerNode->count() > 0;
        }
        return $this->hasChildren;
    }

    public function toArray(): array
    {
        return $this->innerList;
    }

    public function toString(): string
    {
        $result = '';
        for ($i = 0; $i < $this->count(); $i++) {
            $result .= $this->innerList[$i] . "\n";
        }

        return $result;
    }
}
