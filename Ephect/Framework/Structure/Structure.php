<?php

namespace Ephect\Framework\Structure;

use Error;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

class Structure implements StructureInterface
{

    public function __construct(object|array|null $props = null)
    {
        if (!is_array($props) && !is_object($props)) {
            return;
        }

        foreach ($props as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new Error("The property [$key] is not defined.");
            }

            $this->{$key} = $value;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function encode(): string
    {
        $result = $this->recursiveEncode($this);

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    public function decode(string|array $input): void
    {
        $array = $input;
        if (is_string($input)) {
            $array = json_decode($input, true);
        }

        $this->recursiveDecode($this, $array);
    }

    private function recursiveEncode(StructureInterface $structure)
    {
        $result = new stdClass;

        $ref = new ReflectionClass($structure);
        $publicProps = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop) {
            $attrs = $prop->getAttributes();
            $propName = $prop->getName();
            $propType = $prop->getType();
            $propValue = $prop->getValue($structure);

            $resultPropName = $propName;

            foreach ($attrs as $attr) {
                if ($attr->getName() !== JsonProperty::class) {
                    continue;
                }

                $args = $attr->getArguments();
                $argName = $args['name'];

                $resultPropName = $argName;
                break;
            }

            if (!$propType->isBuiltin() && in_array(StructureInterface::class, class_implements($propType->getName()))) {
                $result->{$resultPropName} = $this->recursiveEncode($propValue);
            } else {
                $result->{$resultPropName} = $propValue;
            }
        }

        return $result;
    }

    private function recursiveDecode(StructureInterface $structure, array $values)
    {
        $ref = new ReflectionClass($structure);
        $publicProps = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop) {
            $attrs = $prop->getAttributes();
            $propName = $prop->getName();
            $propType = $prop->getType();

            foreach ($attrs as $attr) {
                if ($attr->getName() !== JsonProperty::class) {
                    continue;
                }

                $args = $attr->getArguments();
                $argName = $args['name'];

                if (isset($values[$argName])) {
                    $values[$propName] = $values[$argName];
                }
                break;
            }

            if (!property_exists($structure, $propName)) {
                throw new Error("The property [$propName] is not defined.");
            }

            if(isset($values[$propName])) {
                if (!$propType->isBuiltin() && in_array(StructureInterface::class, class_implements($propType->getName()))) {
                    $propClass = $propType->getName();
                    $structureChild = new $propClass;
                    $structure->{$propName} = $this->recursiveDecode($structureChild, $values[$propName]);
                } else {
                    $structure->{$propName} = $values[$propName];
                }
            }
        }

        return $structure;
    }

}
