<?php

namespace Ephect\Components;

use Ephect\Components\ComponentStructure;
use Ephect\Core\StructureInterface;
use Ephect\ElementInterface;
use Ephect\ElementTrait;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\Tree;
use Ephect\Tree\TreeInterface;
use Ephect\Tree\TreeTrait;

/**
 * Description of match
 *
 * @author david
 */
class ComponentEntity implements ElementInterface, StructureInterface, TreeInterface
{
    use ElementTrait;
    use TreeTrait;

    protected $parentId = 0;
    protected $name = '';
    protected $text = '';
    protected $start = 0;
    protected $end = 0;
    protected $depth = 0;
    protected $isSibling = false;
    protected $closer = '';
    protected $contents = null;
    protected $hasCloser = '';
    protected $properties = array();
    protected $method = '';
    protected $doc = null;
    protected $viewName = '';
    protected $className = '';
    protected $attributes = null;
    protected $children = null;

    public function __construct(ComponentStructure $attributes)
    {
        $this->id = $attributes->id;
        $this->className = $attributes->class;
        $this->viewName = $attributes->view;
        $this->parentId = $attributes->parentId;
        $this->text = $attributes->text;
        $this->name = $attributes->name;
        $this->start = $attributes->startsAt;
        $this->end = $attributes->endsAt;
        $this->depth = $attributes->depth;
        $this->closer = $attributes->closer;
        $this->contents = is_array($this->closer) ? $this->closer['contents'] : null;
        $this->properties = $attributes->props;
        $this->hasCloser = isset($this->closer);
        $this->method = $attributes->method;
        $this->attributes = $attributes;

        $this->children = $attributes->node;

        $this->innerNode = new Tree();
        // $this->bindNode();

    }

    

    public function bindNode(): void
    {

        if ($this->children === null || $this->children === false) {
            return;
        }

        foreach($this->children as $child) {
            $childEntity = new ComponentEntity(new ComponentStructure($child));
            $this->node()->add($childEntity);
        }
    }

    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function properties($key)
    {
        $result = false;
        if (isset($this->properties[$key])) {
            $result = $this->properties[$key];
        }
        return $result;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getContents(): ?string
    {
        if ($this->contents === null) {
            return null;
        }

        $s = $this->contents['startsAt'];
        $e = $this->contents['endsAt'];

        if ($e - $s < 1) {
            return '';
        }

        ComponentRegistry::uncache();
        $viewFile = ComponentRegistry::read($this->viewName);
        if ($viewFile === null) {
            return null;
        }
        $t = Utils::safeRead(SRC_COPY_DIR . $viewFile);
        $contents = substr($t, $s, $e - $s + 1);

        return $contents;
    }

    public function getChildName(): string
    {
        return $this->childName;
    }

    public function hasChildren(): bool
    {
        return $this->hasChildren;
    }

    public function hasCloser(): bool
    {
        return $this->hasCloser;
    }

    public function isSibling(): bool
    {
        return $this->isSibling;
    }

    public function getCloser(): array
    {
        return $this->closer;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
