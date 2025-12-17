<?php

namespace Ephect\Hooks;

use Closure;
use ReflectionException;
use ReflectionFunction;
use stdClass;

// function useProps(Closure $callback, ?array $defaults = null, ?object $props = null): void
/**
 * Deprecated
 *
 * @param Closure $callback
 * @param object|null $props
 * @return void
 * @throws ReflectionException
 */
#[Deprecated("Useless function", "useEffect", "0.3")]
function useProps(Closure $callback, ?object $props = null): void
{
    // $defaults = $defaults;

    $ref = new ReflectionFunction($callback);
    $params = $ref->getStaticVariables();
    $defaults = $params;
    // $params = $ref->getParameters();

    $hasProps = $props !== null;

    $newProps = !$hasProps ? new stdClass() : $props;
    $newArgs = [];

    foreach ($params as $name => $param) {
        if ($name == 'props') {
            continue;
        }
        $prop = $name;
        // $prop = $param->getName();

        if ($hasProps) {
            if (!isset($newProps->$prop)) {
                // $newProps->$prop = $defaults[$prop];
                $newArgs[] = $defaults[$prop];
            } else {
                $newProps->$prop = $props->$prop;
                $newArgs[] = $props->$prop;
            }
        } else {
            if (!isset($newProps->$prop)) {
                $newProps->$prop = $defaults[$prop];
            }
            $newArgs[] = $newProps->$prop;
        }
    }

    call_user_func_array($callback, $newArgs);
}
