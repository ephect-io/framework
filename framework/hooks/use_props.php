<?php

namespace Ephect\Hooks;

use Closure;
use ReflectionFunction;
use stdClass;

function useProps(array|object|null $props, Closure $callback): array
{
    
    $ref = new ReflectionFunction($callback);
    $params = $ref->getParameters();

    $isNull = $props === null || (is_array($props) && count($props) === 0);

    $newProps = $isNull ? new stdClass : $props;
    $newArgs = [];

    foreach($params as $param) {
        if($param->getName() == 'props') {
            continue;
        }
        $prop = $param->getName();

        if($isNull) {
            $newProps->$prop = '';
            $newArgs[] = '';
        } else {
            $newArgs[] = $newProps->$prop;
        }
    }

    array_unshift($newArgs, $newProps);

    $result = call_user_func($callback, ...$newArgs);

    return $result;
}
