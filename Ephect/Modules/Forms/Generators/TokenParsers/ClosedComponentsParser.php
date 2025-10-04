<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

final class ClosedComponentsParser extends AbstractComponentParser
{
    /**
     * @throws \ReflectionException
     */
    public function do(null|string|array|object $parameter = null): void
    {
        $this->result = [];
        $this->useTypes = $this->parent ? $this->parent->getUses() : [];

        $comp = $this->component;
        $decl = $comp->getDeclaration();
        // First level entity
        $fle = $decl->getComposition();

        $parent = null;
        $child = null;
        if ($parameter != null) {
            [$parent, $child] = $parameter;
        }
        $motherUID = $comp->getMotherUID();

        if ($fle === null) {
            return;
        }
        $props = $this->doArgumentsToString($decl->getArguments()) ?? '';

        if ($decl->hasAttributes()) {
            $this->declareMiddlewares(
                $motherUID,
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
            ComponentEntityInterface $child,
            int $index
        ) use (
            &$subject,
            &$result,
            $parent,
            $motherUID
        ) {

            if ($child->hasCloser()) {
                return;
            }

            $uid = $child->getUID();
            $component = $child->getText();
            $componentName = $child->getName();
            $componentArgs = [];
            $componentArgs['uid'] = $uid;

            $props = '';
            if ($child->props() !== null) {
                $componentArgs = array_merge($componentArgs, $child->props());
                $propsArgs = self::doArgumentsToString($componentArgs);
                $props = "(object) " . $propsArgs ?? "[]";
            }

            Logger::create()->debug($this->useTypes);

            $fqFuncName = ComponentRegistry::read($componentName);
            $componentRender = "\t\t\t<?php \$fn = {$componentName}($props); \$fn(); ?>\n";

            $subject = str_replace($component, $componentRender, $subject);

            $filename = $this->component->getSourceFilename();
            File::safeWrite(
                \Constants::BUILD_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename,
                $subject
            );

            $decl = ComponentDeclaration::byName($fqFuncName);
            $this->declareMiddlewares($motherUID, $parent, $decl, $fqFuncName, $props);

            $this->result[] = $componentName;

            /**
             * TODO Make a listener for this feature
             */
            //            $attributesEvent = new ComponentAttributesEvent($this->component, $child);
            //            $dispatcher = new EventDispatcher();
            //            $dispatcher->dispatch($attributesEvent);
        };

        if ($child != null) {
            $closure($child, 0);
        } elseif (!$fle->hasChildren()) {
            $closure($fle, 0);
        } elseif ($fle->hasChildren()) {
            $fle->forEach($closure, $fle);
        }

        $this->html = $subject;
    }
}
