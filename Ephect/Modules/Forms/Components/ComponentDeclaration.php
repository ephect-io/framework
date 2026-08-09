<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\Element;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\UniqueCodeRegistry;

class ComponentDeclaration extends Element implements ComponentDeclarationInterface
{
    
    protected string $name = '';
    protected mixed $returnType = '';
    protected ?ComponentEntity $entity = null;
    protected mixed $arguments = [];
    protected mixed $attributes = [];
    protected mixed $argumentsTypes = [];
    protected mixed $flatComposition = [];

    public function __construct(ComponentDeclarationStructure $struct)
    {
        parent::__construct($this);

        $this->uid = $struct->uid;
        $this->class = $struct->className;
        $this->returnType = $struct->returnType;
        $this->arguments = $struct->arguments;
        $this->attributes = $struct->attributes;
        $this->argumentsTypes = $struct->argumentsTypes;
        $this->flatComposition = $struct->composition;
    }

    public static function byName(string $componentName): ComponentDeclaration
    {
        $list = CodeRegistry::read($componentName);
        $struct = new ComponentDeclarationStructure($list);
        return new static($struct);
    }

    public static function uniqueByName(string $componentName): ComponentDeclaration
    {
        $list = UniqueCodeRegistry::read($componentName);
        $struct = new ComponentDeclarationStructure($list);
        return new static($struct);
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasArguments(): bool
    {
        return $this->arguments !== null && count($this->arguments) > 0;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }

    public function getArgumentsTypes(): ?array
    {
        return $this->argumentsTypes;
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
