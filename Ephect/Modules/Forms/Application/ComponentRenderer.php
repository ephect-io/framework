<?php

namespace Ephect\Modules\Forms\Application;

use Ephect\Modules\WebApp\Web\Request;
use ReflectionFunction;
use stdClass;

class ComponentRenderer
{
    public static function renderHTML(string $cacheFilename, string $fqFunctionName, array|object|null $functionArgs = null, ?Request $request = null): string
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