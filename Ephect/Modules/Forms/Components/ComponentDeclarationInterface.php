<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\ElementInterface;

interface ComponentDeclarationInterface extends ElementInterface
{
    function getType(): string;

    function getName(): string;

    function hasArguments(): bool;

    function getArguments(): ?array;

    function getComposition(): ?ComponentEntity;
}