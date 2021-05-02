<?php

namespace Ephect\Components;

use Ephect\Element;

class ComponentDeclaration extends Element implements ComponentDeclarationInterface
{
    protected $type = '';
    protected $name = '';
    protected $entity = null;
    protected $arguments = [];
    protected $flatComposition = [];

    function __construct(ComponentDeclarationStructure $struct)
    {
        $this->type = $struct->type;
        $this->arguments = $struct->arguments;
        $this->flatComposition = $struct->composition;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasArguments(): bool
    {
        return count($this->arguments) > 0;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }

    public function getComposition(): ?ComponentEntity
    {
        if($this->entity === null) {
            $this->entity = ComponentEntity::buildFromArray($this->flatComposition);
        }

        return $this->entity;
    }

}