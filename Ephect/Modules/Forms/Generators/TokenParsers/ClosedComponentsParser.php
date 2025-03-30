<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

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
        if ($parameter != null) {
            [$parent, $child] = $parameter;
        }
        $muid = $comp->getMotherUID();

        if ($cmpz === null) {
            return;
        }
        $props = $this->doArgumentsToString($decl->getArguments()) ?? '';

        if ($decl->hasAttributes()) {
            $this->declareMiddlewares(
                $muid,
                $parent,
                $decl,
                $this->component->getFullyQualifiedFunction(),
                $props,
            );
        }

        $subject = $this->html;

        /**
         * @throws \ReflectionException
         */
        $closure = function (
            ComponentEntityInterface $item,
            int $index
        ) use (
            &$subject,
            &$result,
            $parent,
            $muid
        ) {

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

            $fqFuncName = ComponentRegistry::read($componentName);
            $componentRender = "\t\t\t<?php \$fn = \\{$fqFuncName}($props); \$fn(); ?>\n";

            $subject = str_replace($component, $componentRender, $subject);

            $filename = $this->component->getSourceFilename();
            File::safeWrite(\Constants::CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

//            $this->declareMiddlewares($parent, $muid, $fqFuncName, $props, $hasAttrs);
            $decl = ComponentDeclaration::byName($fqFuncName);
            $this->declareMiddlewares($muid, $parent, $decl, $fqFuncName, $props);

            $this->result[] = $componentName;

            /**
             * TODO Make a listener for this feature
             */
//            $attributesEvent = new ComponentAttributesEvent($this->component, $item);
//            $dispatcher = new EventDispatcher();
//            $dispatcher->dispatch($attributesEvent);
        };

        if ($child != null) {
            $closure($child, 0);
        } elseif (!$cmpz->hasChildren()) {
            $closure($cmpz, 0);
        } elseif ($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        }

        $this->html = $subject;
    }
}
