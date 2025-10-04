<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Events\ComponentAttributesEvent;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

class ComponentAttributesListener implements EventListenerInterface
{
    /**
     * @param Event|ComponentAttributesEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ComponentAttributesEvent $event): void
    {
        $funcName = $event->getComponent()->getClass();

        if ($funcName === Component::class) {
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

        $attrs = $event->getAttributes();

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
                        $middleware->parse(null, $motherUID, $funcName, $event->getPropsToString(), $arguments);
                    }

                    StateRegistry::saveByMotherUid($motherUID, true);
                }
            }
        }
    }
}
