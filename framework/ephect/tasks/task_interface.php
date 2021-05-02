<?php

namespace Ephect\Tasks;

use Closure;
use Ephect\ElementInterface;

interface TaskInterface extends ElementInterface
{
    public function getName(): string;
    public function getArguments(): array;
    public function setCallback(Closure $closure): void;
    public function getCallback(): Closure;
}