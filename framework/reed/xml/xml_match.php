<?php
namespace Reed\Xml;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FunCom\Element;

/**
 * Description of match
 *
 * @author david
 */
class XmlMatch extends Element
{
    //put your code here

    private $_parentId = 0;
    private $_name = '';
    private $_text = '';
    private $_start = 0;
    private $_end = 0;
    private $_depth = 0;
    private $_isSibling = false;
    private $_childName = '';
    private $_hasChildren = false;
    private $_closer = '';
    private $_hasCloser = '';
    private $_properties = array();
    private $_method = '';
    private $_isRegistered = false;

    //$text, $groups, $position, $start, $end, $childName, $closer
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->_parentId = $attributes['parentId'];
        $this->_text = $attributes['element'];
        $this->_tmpText = $this->_text;
        $this->_name = $attributes['name'];
        $this->_start = $attributes['startsAt'];
        $this->_end = $attributes['endsAt'];
        $this->_depth = $attributes['depth'];
        $this->_closer = (isset($attributes['closer'])) ? $attributes['closer'] : NULL;
        $this->_childName = $attributes['childName'];
        $this->_properties = $attributes['properties'];
        $this->_hasCloser = isset($this->_closer);
        $this->_method = $attributes['method'];
        $this->_isRegistered = $attributes['isRegistered'];

        $this->_hasChildren = !empty($this->_childName);
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

    public function properties($key)
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

    public function hasCloser(): bool
    {
        return $this->_hasCloser;
    }
   
    public function isSibling(): bool
    {
        return $this->_isSibling;
    }
    
    public function getCloser(): array
    {
        return $this->_closer;
    }
 
    public function getMethod(): string
    {
        return $this->_method;
    }

    public function isRegistered(): bool
    {
        return $this->_isRegistered;
    }
}
