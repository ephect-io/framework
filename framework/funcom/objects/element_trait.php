<?php

namespace FunCom;

trait ElementTrait
{
    protected $parent = null;
    protected $uid = '';
    protected $id = 'noname';
    protected $type = '';
    protected $namespace = '';
    protected $fqClassName = '';

    public function getUID(): string
    {
        if ($this->uid === '') {
            $this->uid = str_replace('.', '_', uniqid(time(), true));
        }
        return $this->uid;
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

    public function getType(): string
    {
        if ($this->type === '') {
            $typeParts = explode('\\', $this->getFullType());
            $this->type = array_pop($typeParts);
            $this->namespace = implode('\\', $typeParts);
        }

        return $this->type;
    }

    public function getFullType(): string
    {
        if ($this->fqClassName == '') {
            $this->fqClassName = get_class($this);
        }

        return $this->fqClassName;
    }

    public function getBaseType(): string
    {
        return get_parent_class($this);
    }

    public function getNamespace(): string
    {
        if($this->namespace === '') {
            $this->getType();
        }

        return $this->namespace;
    }

}
