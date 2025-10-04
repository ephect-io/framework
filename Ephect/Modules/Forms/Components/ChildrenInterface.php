<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\ElementInterface;
use Ephect\Framework\Tree\TreeInterface;

interface ChildrenInterface extends TreeInterface, ElementInterface
{
    public function getAttributes(): string;

    public function getAttribute(string $attribute): string|bool|null;

    public function parentProps(): array|object;

    public function props(): array|object;

    public function getAllProps(): array|object;

    public function render(): void;

    public function getName(): string;
}
