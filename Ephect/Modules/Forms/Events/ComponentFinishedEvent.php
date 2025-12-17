<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\ElementInterface;
use Ephect\Framework\Event\Event;
use Ephect\Modules\Forms\Components\ComponentDeclarationInterface;
use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\Forms\Generators\TokenParsers\AbstractComponentParser;

class ComponentFinishedEvent extends Event
{
    public function __construct(
        private readonly ComponentInterface $component,
        private readonly string $cacheFilename,
    ) {
    }

    public function getMotherUID(): string
    {
        return $this->component->getMotherUID();
    }

    public function getUID(): string
    {
        return $this->component->getUID();
    }

    public function getCacheFilename(): string
    {
        return $this->cacheFilename;
    }

    public function getParent(): ?ElementInterface
    {
        return $this->getDeclaration()->getParent();
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    public function getDeclaration(): ComponentDeclarationInterface
    {
        return $this->component->getDeclaration();
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
