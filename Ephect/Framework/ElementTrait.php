<?php

namespace Ephect\Framework;

use Ephect\Framework\Crypto\Crypto;

trait ElementTrait
{
    protected $parent = null;
    protected ?string $uid = '';
    protected ?string $motherUID = '';
    protected string $id = 'noname';
    protected ?string $class = '';
    protected string $namespace = '';
    protected string $function = '';

    public static function functionName($fullQualifiedName): string
    {
        $fqFunctionName = explode('\\', $fullQualifiedName);
        return array_pop($fqFunctionName);
    }

    public function getUID(): string
    {
        if ($this->uid === '') {
            $this->uid = Crypto::createUID();
        }
        return $this->uid;
    }

    public function getMotherUID(): string
    {
        return $this->motherUID;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function setId($value): void
    {
        $this->id = $value;
    }

    public function getParent(): ?ElementInterface
    {
        return $this->parent;
    }

    public function getBaseclass(): string
    {
        return get_parent_class($this);
    }

    public function getNamespace(): string
    {

        if ($this->namespace === '') {
            $classParts = explode('\\', $this->getclass());
            $this->function = array_pop($classParts);
            $this->namespace = implode('\\', $classParts);
        }

        return $this->namespace;
    }

    public function getClass(): string
    {
        if ($this->class == '') {
            $this->class = get_class($this);
        }

        return $this->class;
    }
}
