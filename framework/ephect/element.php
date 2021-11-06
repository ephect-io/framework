<?php

namespace Ephect;

use DateTime;
use \ReflectionClass;

class Element extends StaticElement implements ElementInterface
{

    use ElementTrait;

    private $_reflection = null;
    protected $serialFilename = '';
    protected $isSerialized = '';
    protected $children = [];
    protected $fqClassName = '';

    public function __construct(?ElementInterface $parent = null, ?string $id = null)
    {
        $this->parent = $parent;
        $this->id = ($id === null) ? '_' . time() : $id;
    }

    public function getReflection(): ?ReflectionClass
    {
        if ($this->_reflection == NULL) {
            $this->_reflection = new ReflectionClass(get_class($this));
        }
        return $this->_reflection;
    }

    public function getMethodParameters($method): ?array
    {
        $ref = $this->getReflection();
        $met = $ref->getMethod($method);

        $params = [];
        foreach ($met->getParameters() as $currentParam) {
            array_push($params, $currentParam->name);
        }

        return $params;
    }

    public function addChild(ElementInterface $child)
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


    public static function getAttributesData(object $instance): array
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
}
