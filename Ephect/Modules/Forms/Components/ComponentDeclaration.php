<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\Element;

class ComponentDeclaration extends Element implements ComponentDeclarationInterface
{
    protected mixed $type = '';
    protected string $name = '';
    protected ?ComponentEntity $entity = null;
    protected mixed $arguments = [];
    protected mixed $attributes = [];
    protected mixed $flatComposition = [];

    function __construct(ComponentDeclarationStructure $struct)
    {
        parent::__construct($this);

        $this->uid = $struct->uid;
        $this->type = $struct->type;
        $this->arguments = $struct->arguments;
        $this->attributes = $struct->attributes;
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

    public function hasAttributes(): bool
    {
        return count($this->attributes) > 0;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function getComposition(): ?ComponentEntity
    {
        if ($this->entity === null) {
            $this->entity = ComponentEntity::buildFromArray($this->flatComposition);
        }

        return $this->entity;
    }

}