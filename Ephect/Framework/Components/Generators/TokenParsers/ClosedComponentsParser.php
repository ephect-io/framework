<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Components\ComponentParserMiddlewareInterface;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Plugins\Route\RouteParserMiddleware;
use Ephect\Framework\Registry\ComponentRegistry;

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


            if($parent !== null) {
// && $parent->getName() == 'Route'

                $middlewaresList = StateRegistry::item('ComponentParserMiddlewares');
                if(count($middlewaresList)) {
                    foreach ($middlewaresList as $middlewareInfo) {
                        [$filename, $middlewareClass] = $middlewareInfo;
                        include_once $filename;
                        if($middlewareClass instanceof ComponentParserMiddlewareInterface) {
                            $middleware = new $middlewareClass;
                            $middleware->parse($parent, $muid, $funcName, $props);
                        }
                    }
                }

                $parserMiddleware = new RouteParserMiddleware();
                $parserMiddleware->parse($parent, $muid, $funcName, $props);
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
