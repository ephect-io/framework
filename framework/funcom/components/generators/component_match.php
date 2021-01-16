<?php

namespace FunCom\Components\Generators;

use FunCom\ElementInterface;
use FunCom\ElementTrait;

/**
 * Description of match
 *
 * @author david
 */
class ComponentMatch implements ElementInterface
{
    use ElementTrait;

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
    private $_contents = null;
    private $_hasCloser = '';
    private $_properties = array();
    private $_method = '';
    private $_isRegistered = false;
    private $_doc = null;

    //$text, $groups, $position, $start, $end, $childName, $closer
    public function __construct(array $attributes, ComponentDocument $doc)
    {
        $this->_doc = $doc;
        $this->id = $attributes['id'];
        $this->_parentId = $attributes['parentId'];
        $this->_text = $attributes['component'];
        $this->_tmpText = $this->_text;
        $this->_name = $attributes['name'];
        $this->_start = $attributes['startsAt'];
        $this->_end = $attributes['endsAt'];
        $this->_depth = $attributes['depth'];
        $this->_closer = (isset($attributes['closer'])) ? $attributes['closer'] : null;
        $this->_contents = ($this->_closer !== null) ? $this->_closer['contents'] : null;
        // $this->_childName = $attributes['childName'];
        $this->_properties = $attributes['props'];
        $this->_hasCloser = isset($this->_closer);
        $this->_method = $attributes['name'];

        // $this->_hasChildren = !empty($this->_childName);
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

    public function getContents(): ?string
    {
        if($this->_contents === null) {
            return null;
        }

        $s = $this->_contents['startsAt'];
        $e = $this->_contents['endsAt'];

        if($e - $s < 1) {
            return '';
        }

        $t = $this->_doc->getText();
        $contents = substr($t, $s, $e - $s + 1);

        return $contents;
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

}
