<?php

namespace Ephect\Framework\Components\Application;

use Ephect\Framework\Web\Request;
use Ephect\Plugins\Router\RouterService;
use ReflectionFunction;
use stdClass;

class ComponentRenderer
{
    public static function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null, ?Request $request = null): string
    {
        include_once CACHE_DIR . $cacheFilename;

        $funcReflection = new ReflectionFunction($fqFunctionName);
        $funcParams = $funcReflection->getParameters();

        $bodyProps = null;
        if ($request !== null && $request->headers->contains('application/json', 'content-type')) {
            $bodyProps = json_decode($request->body);
        }

        $html = '';

        if ($funcParams === [] && $bodyProps === null) {
            ob_start();
            $fn = call_user_func($fqFunctionName);
            $fn();
            $html = ob_get_clean();
        } else {
            $props = null;
            if (count($functionArgs) > 0) {
                $props = $functionArgs;
            } else {
                $routeProps = RouterService::findRouteArguments($fqFunctionName);
                if ($routeProps !== null) {
                    $props = new stdClass;
                    foreach ($routeProps as $field => $value) {
                        $props->{$field} = null;
                    }
                }
            }

            if ($bodyProps !== null) {
                if ($props === null) {
                    $props = new stdClass;
                }
                foreach ($bodyProps as $field => $value) {
                    $props->{$field} = $value;
                }
            }
            ob_start();
            $fn = call_user_func($fqFunctionName, $props);
            $fn();
            $html = ob_get_clean();

        }

        return $html;
    }
}