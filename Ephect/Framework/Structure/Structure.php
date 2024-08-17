<?php

namespace Ephect\Framework\Structure;

use Error;
use ReflectionClass;
use stdClass;

class Structure implements StructureInterface
{

    public function __construct(?array $props = null)
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
        $result = new stdClass;

        $ref  = new ReflectionClass($this);
        $publicProps = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop) {
            $attrs = $prop->getAttributes();
            $propName = $prop->getName();
            $resultPropName = $propName;

            foreach ($attrs as $attr) {
                if($attr->getName() !== JsonProperty::class) {
                    continue;
                }

                $args = $attr->getArguments();
                $argName = $args['name'];

                $resultPropName = $argName;
                break;
            }

            $result->{$resultPropName} = $this->{$propName};
        }

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    public function decode(string $serialized): void
    {
        $array = json_decode($serialized, true);

        $ref  = new ReflectionClass($this);
        $publicProps = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop) {
            $attrs = $prop->getAttributes();
            $propName = $prop->getName();

            foreach ($attrs as $attr) {
                if($attr->getName() !== JsonProperty::class) {
                    continue;
                }

                $args = $attr->getArguments();
                $argName = $args['name'];

                $array[$propName] = $array[$argName];
                break;
            }

            $this->{$propName} = $array[$propName];
        }
    }

}
