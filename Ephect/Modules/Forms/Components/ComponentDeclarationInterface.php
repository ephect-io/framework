<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\ElementInterface;

interface ComponentDeclarationInterface extends ElementInterface
{
    public function getType(): string;

    public function getName(): string;

    public function hasArguments(): bool;

    public function hasAttributes(): bool;

    public function getArguments(): ?array;

    public function getComposition(): ?ComponentEntity;
}
