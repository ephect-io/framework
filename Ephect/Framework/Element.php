<?php

namespace Ephect\Framework;

use ReflectionClass;
use ReflectionException;

class Element extends StaticElement implements ElementInterface
{

    use ElementTrait;

    protected array $children = [];
    private ?ReflectionClass $_reflection = null;

    public function __construct(?ElementInterface $parent = null, ?string $id = null)
    {
        parent::__construct();

        $this->parent = $parent;
        $this->id = ($id === null) ? '_' . time() : $id;
    }

    public static function getClassAttributesData(object $instance): array
    {
        $result = [];
        $temp = [];

        $reflection = new ReflectionClass($instance);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $args = $attribute->getArguments();

            if (isset($temp[$name])) {
                $temp[$name] = array_merge($temp[$name], $args);
            } else {
                $temp[$name] = $args;
            }
        }

        foreach ($temp as $key => $value) {
            $result[] = ["name" => $key, "args" => $value];
        }

        return $result;
    }

    /**
     * @throws ReflectionException
     */
    public function getMethodParameters($method): ?array
    {
        $ref = $this->getReflection();
        $met = $ref->getMethod($method);

        $params = [];
        foreach ($met->getParameters() as $currentParam) {
            $params[] = $currentParam->name;
        }

        return $params;
    }

    public function getReflection(): ?ReflectionClass
    {
        if ($this->_reflection == null) {
            $this->_reflection = new ReflectionClass(get_class($this));
        }
        return $this->_reflection;
    }

    public function addChild(ElementInterface $child): void
    {
        $this->children[$child->getId()] = $child;
    }

    public function removeChild(ElementInterface $child): void
    {
        unset($this->children[$child->getId()]);
    }

    public function getChildById($id): ?object
    {
        $result = null;

        if (array_key_exists($id, $this->children)) {
            $result = $this->children[$id];
        }

        return $result;
    }

    public function getChildrenIds(): ?array
    {
        return array_keys($this->children);
    }

    public function getFileName(): string
    {
        $reflection = $this->getReflection();
        return $reflection->getFileName();
    }
}
