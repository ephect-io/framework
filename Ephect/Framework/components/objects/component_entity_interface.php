<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\StructureInterface;
use Ephect\Framework\ElementInterface;
use Ephect\Framework\Tree\TreeInterface;

interface ComponentEntityInterface extends TreeInterface, ElementInterface, StructureInterface
{
    public function getParentId(): int;
    public function getName(): string;
    public function getText(): string;
    public function getDepth(): int;
    public function hasProps(): bool;
    public function props(?string $key = null): string|array|null;
    public function getStart(): int;
    public function getEnd(): int;
    public function getContents(): ?string;
    public function getChildName(): string;
    public function hasCloser(): bool;
    public function isSibling(): bool;
    public function getCloser(): array;
    public function getMethod(): string;
}
