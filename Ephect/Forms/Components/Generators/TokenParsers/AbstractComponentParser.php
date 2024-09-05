<?php

namespace Ephect\Forms\Components\Generators\TokenParsers;

use Ephect\Forms\Components\ComponentEntityInterface;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Forms\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\Text;
use ReflectionFunction;

abstract class AbstractComponentParser extends AbstractTokenParser
{

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $arrayString = Text::arrayToString($value);
                $pair = '"' . $key . '" => ' . $arrayString . ', ';
            } else {
                $pair = '"' . $key . '" => ' . (addslashes($value) != $value ? "'" . addslashes($value) . "', " : "'" . $value . "', ");
            }
            if ($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';
            }
            $result .= $pair;
        }
        return ($result === '') ? null : '[' . $result . ']';
    }

    abstract public function do(object|array|string|null $parameter = null): void;

    public function declareMiddlewares(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props): void
    {
        /**
         * Mandatory test: Parent is not always null!
         */
        if ($parent == null) {
            return;
        }

        $filename = $motherUID . DIRECTORY_SEPARATOR . ComponentRegistry::read($funcName);

        if (!is_file(CACHE_DIR . $filename)) {
            return;
        }

        include_once CACHE_DIR . $filename;

        $reflection = new ReflectionFunction($funcName);
        $attrs = $reflection->getAttributes();
        $middlewaresList = [];
        $middlewaresArgsList = [];
        foreach ($attrs as $attr) {
            $attrNew = $attr->newInstance();
            if ($attrNew instanceof AttributeMiddlewareInterface) {
                $middlewaresList[$attr->getName()] = [
                    $attr->getArguments(),
                    $attrNew->getMiddlewares(),
                ];
            }
        }

        if (count($middlewaresList)) {
            FrameworkRegistry::load();
            foreach ($middlewaresList as $key => $value) {
                [$arguments, $middlewares] = $value;
                foreach ($middlewares as $middlewareClass) {
                    $filename = FrameworkRegistry::read($middlewareClass);
                    include_once $filename;
                    $middleware = new $middlewareClass;

                    if ($middleware instanceof ComponentParserMiddlewareInterface) {
                        $middleware->parse($parent, $motherUID, $funcName, $props, $arguments);
                    }

//                    StateRegistry::saveByMotherUid($motherUID, true);
                    StateRegistry::save(true);


                }

            }
        }
    }
}