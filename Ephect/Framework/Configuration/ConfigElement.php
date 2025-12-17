<?php

namespace Ephect\Framework\Configuration;

use Ephect\Framework\Element;

class ConfigElement extends Element
{
    private string $name = '';
    private string $value = '';
    private string $type = '';

    public function __construct($id, $name, $value)
    {
        parent::__construct($this);
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
