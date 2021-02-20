<?php

namespace Ephect\Components;

use Ephect\ElementTrait;
use Ephect\Tree\Tree;
use Ephect\Tree\TreeInterface;

class Composition implements CompositionInterface
{
    protected $class;
    protected $components = null;
    protected $depths = [];

    use ElementTrait;

    public function __construct(string $className)
    {
        $this->getUID();
        $this->class = $className;
        $this->components = new Tree;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getDepths(): array
    {
        return $this->depths;
    }


    public function items(): ?TreeInterface
    {
        return $this->components;
    }


    public function bindNodes(): void
    {

        $list = $this->toArray();
        $this->components = new Tree;

        $c = count($list);
        for ($i = 0; $i < $c; $i++) {
            if ($list[$i]['parentId'] === -1) {
                continue;
            }
            $pId = $list[$i]['parentId'];

            if (!is_array($list[$pId]['node'])) {
                $list[$pId]['node'] = [];
            }
            array_push($list[$pId]['node'], $list[$i]);
            unset($list[$i]);
        }

        foreach ($list as $item) {
            $childEntity = new ComponentEntity(new ComponentStructure($item));
            $this->components->add($childEntity);
        }
    }

    public function toArray(): array
    {
        $result = [];
        foreach($this->components as $component) {
            array_push($result, $component->toArray());
        }

        return $result;
    }
}
