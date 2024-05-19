<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Components\ComponentParserMiddlewareInterface;
use Ephect\Framework\Components\Generators\TokenParsers\Middleware\MiddlewareAttributeInterface;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Registry\ComponentRegistry;
use ReflectionFunction;

final class ClosedComponentsParser extends AbstractComponentParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->result = [];

        $comp = $this->component;
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();
        $parent = null;
        $child = null;
        if($parameter != null) {
            [$parent, $child] = $parameter;
        }
        $muid = $comp->getMotherUID();

        if ($cmpz === null) {
            return;
        }

        $subject = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index) use (&$subject, &$result, $parent, $muid) {

            if ($item->hasCloser()) {
                return;
            }

            $uid = $item->getUID();
            $component = $item->getText();
            $componentName = $item->getName();
            $componentArgs = [];
            $componentArgs['uid'] = $uid;

            $props = '';
            if ($item->props() !== null) {
                $componentArgs = array_merge($componentArgs, $item->props());
                $propsArgs = self::doArgumentsToString($componentArgs);
                $props = "(object) " . $propsArgs ?? "[]";
            }

            $funcName = ComponentRegistry::read($componentName);
            $componentRender = "\t\t\t<?php \$fn = \\{$funcName}($props); \$fn(); ?>\n";

            $subject = str_replace($component, $componentRender, $subject);
            $filename = $muid . DIRECTORY_SEPARATOR . ComponentRegistry::read($funcName);

            if($parent !== null && is_file(CACHE_DIR . $filename)) {

                include_once CACHE_DIR . $filename;

                $reflection = new ReflectionFunction($funcName);
                $attrs = $reflection->getAttributes();
                $middlewaresList = [];
                foreach ($attrs as $attr) {
                    $attrNew = $attr->newInstance();
                    if($attrNew instanceof MiddlewareAttributeInterface) {
                        $attrMiddlewares = $attrNew->getMiddlewares();
                        $middlewaresList = [...$middlewaresList, ...$attrMiddlewares];
                    }
                }

                 if(count($middlewaresList)) {
                     FrameworkRegistry::uncache();
                     foreach ($middlewaresList as $middlewareClass) {
                         $filename = FrameworkRegistry::read($middlewareClass);

                         include_once $filename;
                         $middleware = new $middlewareClass;

                         if($middleware instanceof ComponentParserMiddlewareInterface) {
                             $middleware->parse($parent, $muid, $funcName, $props);
                         }
                     }
                 }
            }

            $this->result[] = $componentName;

            $filename = $this->component->getFlattenSourceFilename();
            File::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);
        };

        if($child != null) {
            $closure($child, 0);
        } else if (!$cmpz->hasChildren()) {
            $closure($cmpz, 0);
        } else if ($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        }

        $this->html = $subject;
    }

}
