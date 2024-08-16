<?php

namespace Ephect\Framework\Structure;

use Ephect\Framework\Element;
use Error;

class Structure implements StructureInterface
{

    public function __construct($props)
    {
        if (!is_array($props) && !is_object($props)) {
            return null;
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

    public static function encode(StructureInterface $structure)
    {

        $attrs = Element::getClassAttributesData($structure);

        foreach ($attrs as $attr) {
            if($attr['name'] !== "JsonProperty") {
                continue;
            }

            $property = $attr['args']['name'];


        }
    }
    public static function decode($serialized)
    {

    }
}
