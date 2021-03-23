<?php

namespace Ephect\Components;

use Ephect\Core\StructureInterface;
use Ephect\ElementInterface;
use Ephect\Tree\TreeInterface;

interface ComponentEntityInterface extends TreeInterface, ElementInterface, StructureInterface
{
    public function getParentId(): int;
    public function getName(): string;
    public function getText(): string;
    public function getDepth(): int;
    public function properties(string $key);
    public function getStart(): int;
    public function getEnd(): int;
    public function getContents(): ?string;
    public function getChildName(): string;
    public function hasCloser(): bool;
    public function isSibling(): bool;
    public function getCloser(): array;
    public function getMethod(): string;
}
