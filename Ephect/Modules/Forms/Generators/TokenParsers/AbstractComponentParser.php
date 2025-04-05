<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\Text;
use Ephect\Modules\Forms\Components\ComponentDeclarationInterface;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;

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
                $pair = '"' . $key . '" => ' . (
                    addslashes($value) != $value
                    ? "'" . addslashes($value) . "', "
                    : "'" . $value . "', "
                );
            }
            if ($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';
            }
            $result .= $pair;
        }
        return ($result === '') ? null : '[' . $result . ']';
    }

    abstract public function do(object|array|string|null $parameter = null): void;

    /**
     * @throws \ReflectionException
     */
    public function declareMiddlewares(
        string $motherUID,
        ComponentEntityInterface|null $parent,
        ?ComponentDeclarationInterface $declaration,
        string $fqItemName,
        string $props
    ): void {

        /**
         * Mandatory test: Parent is not always null!
         */
        if ($declaration == null || !$declaration->hasAttributes()) {
            return;
        }

        $attrs = $declaration->getAttributes();

        $middlewaresList = [];
        foreach ($attrs as $attr) {
            if (!isset($attr['name'])) {
                continue;
            }

            $attr = (object)$attr;

            if (count($attr->arguments) > 0) {
                $attrNew = new $attr->name(...$attr->arguments);
            } else {
                $attrNew = new $attr->name();
            }

            if ($attrNew instanceof AttributeMiddlewareInterface) {
                $middlewaresList[$attr->name] = [
                    $attr->arguments,
                    $attrNew->getMiddlewares(),
                ];
            }
        }

        if (count($middlewaresList)) {
            foreach ($middlewaresList as $key => $value) {
                [$arguments, $middlewares] = $value;
                foreach ($middlewares as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if ($middleware instanceof ComponentParserMiddlewareInterface) {
                        Logger::create()->debug([
                            'classname' => $fqItemName,
                            'middleware' => $middlewareClass
                        ]);
                        $middleware->parse($parent, $motherUID, $fqItemName, $props, $arguments);
                    }

                    StateRegistry::saveByMotherUid($motherUID, true);
                }
            }
        }
    }
}
