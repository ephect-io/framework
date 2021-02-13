<?php

namespace FunCom\Components;

use FunCom\ElementTrait;

class Composition implements CompositionInterface
{
    protected $class;
    protected $components;

    use ElementTrait;

    public function __construct(string $className, array $components)
    {
        $this->getUID();
        $this->class = $className;
        $this->components = $components;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->components as $component) {
            array_push($result, $component->toArray());
        }
        
        return $result;
    }
}
