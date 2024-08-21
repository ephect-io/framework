<?php

namespace Ephect\Framework\Configuration;

use Ephect\Framework\Element;

class ConfigElement extends Element
{

    private $_name = '';
    private $_value = '';
    private $_type = '';

    public function __construct($id, $name, $value)
    {
        parent::__construct($this);
        $this->id = $id;
        $this->_name = $name;
        $this->_value = $value;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getValue(): string
    {
        return $this->_value;
    }

    public function getType(): string
    {
        return $this->_type;
    }


}
