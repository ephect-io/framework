<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Generators\TokenParsers\AbstractComponentParser;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;

class ComponentFinishedListener implements EventListenerInterface
{


    /**
     * @param Event|ComponentFinishedEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ComponentFinishedEvent $event): void
    {

        $funcName = $event->getComponent()->getClass();

        if ($funcName === 'Ephect\Modules\Forms\Components\Component') {
            return;
        }

        $motherUID = $event->getMotherUID();
        $filename = $event->getCacheFilename();

        $decl = $event->getDeclaration();
        $componentName = $decl->getName();

        if ($componentName == 'FakeFragment') {
            return;
        }

        if ($componentName == 'Fragment') {
            return;
        }

        if ($componentName == 'Slot') {
            return;
        }

        include_once \Constants::CACHE_DIR . $filename;

        $reflection = new \ReflectionFunction($funcName);
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
            Logger::create()->debug([
                $event->getComponent()->getClass(),
                $decl->getName(),
            ], __METHOD__);

            FrameworkRegistry::load();
            foreach ($middlewaresList as $key => $value) {
                [$arguments, $middlewares] = $value;
                foreach ($middlewares as $middlewareClass) {
                    $filename = FrameworkRegistry::read($middlewareClass);
                    include_once $filename;
                    $middleware = new $middlewareClass();

                    if ($middleware instanceof ComponentParserMiddlewareInterface) {
                        $middleware->parse(null, $motherUID, $funcName, $event->getPropsToString(), $arguments);
                    }

                    StateRegistry::saveByMotherUid($motherUID, true);
//                    StateRegistry::save(true);
                }
            }
        }
    }
}