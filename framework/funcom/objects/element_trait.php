<?php

namespace FunCom;

trait ElementTrait
{
    protected $parent = null;
    protected $uid = '';
    protected $id = 'noname';
    protected $type = '';

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
        }

        return $this->type;
    }
}
