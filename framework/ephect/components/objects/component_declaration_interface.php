<?php

namespace Ephect\Components;

use Ephect\Element;
use Ephect\ElementInterface;

interface ComponentDeclarationInterface extends ElementInterface
{
    function getType(): string;
    function getName(): string;
    function hasArguments(): bool;
    function getArguments(): ?array;
    function getComposition(): ?ComponentEntity;
}