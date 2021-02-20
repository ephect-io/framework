<?php

namespace Ephect\Tree;

use Iterator;
//
interface TreeInterface  extends Iterator
{
    public function add($object): int;
    public function insert($object, $index): bool;
    public function update($object, $index): bool;
    public function removeAt($index): bool;
    public function find($object): ?int;
    public function items(?int $index = null);
    public function count(): int;
    public function node(): ?TreeInterface;
}
