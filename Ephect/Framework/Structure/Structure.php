<?php

namespace Ephect\Framework\Structure;

use Error;
use ReflectionClass;
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

            if (!property_exists($this, $propName)) {
                throw new Error("The property [$propName] is not defined.");
            }

            $result->{$resultPropName} = $this->{$propName};
        }

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    public function decode(string|array $input): void
    {
        $array = $input;
        if(is_string($input)) {
            $array = json_decode($input, true);
        }

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

                if(isset($array[$argName])) {
                    $array[$propName] = $array[$argName];
                }
                break;
            }

            if (!property_exists($this, $propName)) {
                throw new Error("The property [$propName] is not defined.");
            }

            if(isset($array[$propName])) {
                $this->{$propName} = $array[$propName];
            }
        }
    }

}
