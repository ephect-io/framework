<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\ElementInterface;

interface ComponentDeclarationInterface extends ElementInterface
{
    public function getName(): string;

    public function getClass(): string;

    public function getReturnType(): string;

    public function hasArguments(): bool;

    public function getArguments(): ?array;

    public function getArgumentsTypes(): ?array;

    public function hasAttributes(): bool;

    public function getAttributes(): ?array;

    public function getComposition(): ?ComponentEntity;
}
