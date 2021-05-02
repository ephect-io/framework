<?php

namespace Ephect\Tasks;

use Closure;
use Ephect\Element;

class Task extends Element implements TaskInterface
{
    protected $name = '';
    protected $arguments = [];
    protected $callback = null;

    public function __construct(TaskStructure $struct)
    {
        $this->name = $struct->name;
        $this->arguments = $struct->arguments;    
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setCallback(Closure $closure): void
    {
        $this->callback = $closure;
    }

    public function getCallback(): Closure
    {
        return $this->callback;
    }
}