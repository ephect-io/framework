<?php

namespace FunCom\Components;

use FunCom\Components\ComponentStructure;
use FunCom\Components\Generators\ComponentDocument;
use FunCom\ElementInterface;
use FunCom\ElementTrait;
use FunCom\IO\Utils;
use FunCom\Registry\ViewRegistry;

/**
 * Description of match
 *
 * @author david
 */
class ComponentEntity implements ElementInterface
{
    use ElementTrait;

    private $_parentId = 0;
    private $_name = '';
    private $_text = '';
    private $_start = 0;
    private $_end = 0;
    private $_depth = 0;
    private $_isSibling = false;
    private $_closer = '';
    private $_contents = null;
    private $_hasCloser = '';
    private $_properties = array();
    private $_method = '';
    private $_doc = null;
    private $_viewName = '';

    public function __construct(ComponentStructure $attributes, ?ComponentDocument $doc)
    {
        $this->_doc = $doc;
        $this->id = $attributes->id;
        $this->_viewName = $attributes->view;
        $this->_parentId = $attributes->parentId;
        $this->_text = $attributes->text;
        $this->_name = $attributes->name;
        $this->_start = $attributes->startsAt;
        $this->_end = $attributes->endsAt;
        $this->_depth = $attributes->depth;
        $this->_closer = $attributes->closer;
        $this->_contents = is_array($this->_closer) ? $this->_closer['contents'] : null;
        $this->_properties = $attributes->props;
        $this->_hasCloser = isset($this->_closer);
        $this->_method = $attributes->method;
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

        // ViewRegistry::uncache();
        // $viewFile = ViewRegistry::read($this->_viewName);
        // if($viewFile === null) {
        //     return null;
        // }
        // $t = Utils::safeRead(SRC_ROOT . $viewFile);
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
