<?php

namespace Ephect\Framework\Structure;

use Ephect\Framework\Element;
use ReflectionClass;

class StructureSerializer
{
    public static function serialize(StructureInterface $structure)
    {

        $attrs = Element::getClassAttributesData($structure);

        foreach ($attrs as $attr) {
            if($attr['name'] !== "JsonProperty") {
                continue;
            }

            $property = $attr['args']['name'];


        }
    }
    public static function unserialize($serialized)
    {

    }
}