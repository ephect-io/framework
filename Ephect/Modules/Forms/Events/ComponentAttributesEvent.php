<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\ElementInterface;
use Ephect\Framework\Event\Event;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentDeclarationInterface;
use Ephect\Modules\Forms\Components\ComponentDeclarationStructure;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\Forms\Generators\TokenParsers\AbstractComponentParser;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

use function Ephect\Hooks\useMemory;

class ComponentAttributesEvent extends Event
{
    private ?ComponentDeclaration $declaration = null;
    private string $buildDirectory;

    public function __construct(
        private readonly ComponentInterface $parent,
        private readonly ComponentEntityInterface $entity,
    ) {
        CodeRegistry::load();
        [$this->buildDirectory] = useMemory(get: 'buildDirectory');
    }

    public function getEntity(): ComponentEntityInterface
    {
        return $this->entity;
    }

    public function getMotherUID(): string
    {
        return $this->parent->getMotherUID();
    }

    public function getUID(): string
    {
        return $this->parent->getUID();
    }

    public function getCacheFilename(): string
    {
        $filename = $this->parent->getSourceFilename();
        return $this->buildDirectory . $this->parent->getMotherUID() . DIRECTORY_SEPARATOR . $filename;
    }

    public function getParent(): ?ElementInterface
    {
        return $this->parent->getParent();
    }

    public function getComponent(): ComponentInterface
    {
        return $this->parent;
    }

    public function getDeclaration(): ComponentDeclarationInterface
    {
        if ($this->declaration === null) {
            $fqFuncName = ComponentRegistry::read($this->entity->getName());
            $list = CodeRegistry::read($fqFuncName);
            $struct = new ComponentDeclarationStructure($list);
            $this->declaration = new ComponentDeclaration($struct);
        }

        return $this->declaration;
    }

    public function getAttributes(): ?array
    {
        return $this->declaration->getAttributes();
    }

    public function getPropsToArray(): array
    {
        $decl = $this->getDeclaration();
        return $decl->getArguments();
    }

    public function getProps(): object
    {
        return (object) $this->getPropsToArray();
    }

    public function getPropsToString(): string
    {
        return AbstractComponentParser::doArgumentsToString($this->getPropsToArray()) ?? '';
    }

}
