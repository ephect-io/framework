<?php

namespace Ephect\Framework\Tree;

use Ephect\Framework\ElementTrait;
use RecursiveIterator;

class TreeIterator implements RecursiveIterator
{
    use ElementTrait;

    protected int $count = 0;
    protected array $elementList = [];
    protected bool $hasElements = false;
    protected int $index = 0;
    protected $innerChildren = null;

    public function __construct(array $list)
    {
        $this->elementList = $list;
    }


    public function count(): int
    {
        $this->count = count($this->elementList);
        return $this->count;
    }

    public function current(): mixed
    {
        if (!$this->valid()) {
            return null;
        }
        return $this->elementList[$this->index];
    }

    public function valid(): bool
    {
        return isset($this->elementList[$this->index]);
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

    public function getChildren(): TreeIterator
    {
        $it = new TreeIterator($this->elementList);
        return $it;
    }

    public function hasChildren(): bool
    {
        if ($this->hasElements === false) {
            $this->hasElements = $this->elementList !== null;
        }
        return $this->hasElements;
    }

    public function toArray(): array
    {
        return $this->elementList;
    }

    public function toString(): string
    {
        $result = '';
        for ($i = 0; $i < $this->count; $i++) {
            $result .= $this->elementList[$i] . "\n";
        }

        return $result;
    }
}
