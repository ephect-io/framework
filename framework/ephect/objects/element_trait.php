<?php

namespace Ephect;

use Ephect\Crypto\Crypto;

trait ElementTrait
{
    protected $parent = null;
    protected $uid = '';
    protected $motherUID = '';
    protected $id = 'noname';
    protected $class = '';
    protected $namespace = '';
    protected $function = '';

    public function getUID(): string
    {
        if ($this->uid === '') {
            // $this->uid = str_replace('.', '_', uniqid(time(), true));
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

    // public function getclass(): string
    // {
    //     if ($this->class === '') {
    //         $classParts = explode('\\', $this->getFullclass());
    //         $this->class = array_pop($classParts);
    //         $this->namespace = implode('\\', $classParts);
    //     }

    //     return $this->class;
    // }

    public function getClass(): string
    {
        if ($this->class == '') {
            $this->class = get_class($this);
        }

        return $this->class;
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

    public static function functionName($fullQualifiedName): string
    {
        $fqFunctionName = explode('\\', $fullQualifiedName);
        $function = array_pop($fqFunctionName);

        return $function;
    }
}
