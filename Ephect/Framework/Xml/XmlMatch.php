<?php

namespace Ephect\Framework\Xml;

use Ephect\Framework\Element;

class XmlMatch extends Element
{
    private int $_parentId = 0;
    private string $_name = '';
    private string $_text = '';
    private int $_start = 0;
    private int $_end = 0;
    private int $_depth = 0;
    private string $_tmpText = '';
    private string $_childName = '';
    private bool $_hasChildren = false;
    private array $_closer = [];
    private array $_properties = [];
    private string $_method = '';

    //$text, $groups, $position, $start, $end, $childName, $closer
    public function __construct(array $array)
    {
        $this->id = $array['id'];
        $this->_parentId = $array['parentId'];
        $this->_text = $array['element'];
        $this->_tmpText = $this->_text;
        $this->_name = $array['name'];
        $this->_start = $array['startsAt'];
        $this->_end = $array['endsAt'];
        $this->_depth = $array['depth'];
        $this->_closer = (isset($array['closer'])) ? $array['closer'] : NULL;
        $this->_childName = $array['childName'];
        $this->_properties = $array['properties'];
        $this->_method = $array['method'];

        $this->_hasChildren = isset($this->_closer);
        if ($this->_hasChildren) {
            $this->_end = $this->_closer['endsAt'];
        }
    }



    public function getParentId(): int
    {
        return $this->_parentId;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getText(): string
    {
        return $this->_text;
    }

    public function getDepth(): int
    {
        return $this->_depth;
    }

    public function properties(string $key)
    {
        $result = false;
        if (isset($this->_properties[$key])) {
            $result = $this->_properties[$key];
        }
        return $result;
    }

    public function getStart(): int
    {
        return $this->_start;
    }

    public function getEnd(): int
    {
        return $this->_end;
    }

    public function getChildName(): string
    {
        return $this->_childName;
    }

    public function hasChildren(): bool
    {
        return $this->_hasChildren;
    }

    public function getCloser(): array
    {
        return $this->_closer;
    }

    public function getMethod(): string
    {
        return $this->_method;
    }
}
