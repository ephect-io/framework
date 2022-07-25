<?php

namespace Ephect\Hooks;

use ReflectionFunction;
use stdClass;

function useEffect($callback, ...$argv)
{

    $args = func_get_args();
    array_shift($args);
    
    $noValidate = function ($callback, $props, $params) {
        $closure = $callback;
        $args = $props;
        $default = $params;

        return $params;
    };

    $parametersToArray = function ($parameters) {
        $result = [];

        foreach($parameters as $key => $param) {
            $result[$param->getName()] = $key;
        }

        return $result;
    };

    $validate = function ($callback, $params, $args) use ($parametersToArray) {

        $params = $parametersToArray($params);

        $props = isset($params['props']) ? $params['props'] : null;
        

        $defaults = $params;

        $hasProps = $props !== null;
        if ($hasProps) {
            $args = (array) $props;
            $hasProps =  (is_array($args) && count($args) > 0);
        }

        $newProps = !$hasProps ? new stdClass : $props;
        $setPropsFirst = false;

        $newArgs = [];

        $l = count($params);
        foreach($params as $name => $key) {
            if ($name == 'props') {
                $setPropsFirst = true;
                continue;
            }

            if(!isset($newProps->name)) {
                $newProps->name = $args[$key];
            }
            $newArgs[$name] = $args[$key];
        }

        if ($setPropsFirst) {
            array_unshift($newArgs, $newProps);
        }

        return $newArgs;
    };



    $ref = new ReflectionFunction($callback);
    // $params = $ref->getStaticVariables();
    $params = $ref->getParameters();


    if(count($params) > 0) {
       $args = $validate($callback, $params, $args);
    }

    call_user_func_array($callback, $args);
}
