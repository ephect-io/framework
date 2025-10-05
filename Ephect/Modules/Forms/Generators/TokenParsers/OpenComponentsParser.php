<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

final class OpenComponentsParser extends AbstractComponentParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->result = [];
        $this->useVariables = $parameter;

        $comp = $this->component;
        $comp->resetDeclaration();
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if ($cmpz === null || !$cmpz->hasCloser()) {
            return;
        }

        $props = $this->doArgumentsToString($decl->getArguments()) ?? '';

        if ($decl->hasAttributes()) {
            $this->declareMiddlewares(
                $this->component->getMotherUID(),
                null,
                $decl,
                $this->component->getFullyQualifiedFunction(),
                $props,
            );
        }

        $subject = $this->html;

        $previous = null;

        /**
         * @throws \ReflectionException
         */
        $closure = function (
            ComponentEntityInterface $item,
            int $index
        ) use (
            $comp,
            &$subject,
            &$result,
            &$previous,
            &$parent
        ) {
            $parent = $previous != null && $previous->getDepth() < $item->getDepth() ? $previous : null;

            if (!$item->hasCloser()) {
                /**
                 * Mandatory for middleware parsing...
                 */
                $p = new ClosedComponentsParser($comp, $this->buildDirectory);
                $p->do([$parent, $item]);
                return;
            }
            $uid = $item->getUID();

            $opener = $item->getText();
            $theCloser = (object)$item->getCloser();
            $closer = $theCloser->text;
            $componentName = $item->getName();
            $componentBody = $item->getContents($subject);

            $componentArgs = $this->useVariables;
            $componentArgs = $item->props() !== null ? array_merge($componentArgs, $item->props()) : $componentArgs;

            if ($componentName == 'FakeFragment') {
                return;
            }

            if ($componentName == 'Fragment') {
                return;
            }

            if ($componentName == 'Slot') {
                return;
            }

            $motherUID = $this->component->getMotherUID();
            $decl = $this->component->getDeclaration();
            $props = self::doArgumentsToString($componentArgs) ?? "[]";

            $propsKeys = $this->argumentsKeys($this->useVariables);

            $useChildren = $decl->hasArguments() || count($propsKeys) ? $this->useArguments($propsKeys) : ' ';

            $className = $this->component->getFullyQualifiedFunction() ?: $componentName;
            $name = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]';

            $fqComponentName = ComponentRegistry::read($componentName);

            $preComponentBody = '';
            $pkey = '$children';
            if (count($propsKeys) === 1 && ($propsKeys[0] === $pkey || $propsKeys[0] === '$slot')) {
                $pkey = $propsKeys[0];
                $preComponentBody = <<<PCB
            <?php if(method_exists({$pkey}, 'props')) {
                \$props = {$pkey}->props();
                foreach(\$props as \$key => \$value) {
                    $\$key = \$value;
                }
            } ?>
PCB;
            }

            $componentRender = <<< PHP
            <?php \$struct = new \\Ephect\\Modules\\Forms\\Components\\ChildrenStructure(['props' => (object) $props, 'buffer' => function()$useChildren{?>
                    $preComponentBody$componentBody
            <?php
            }, 'motherUID' => '$motherUID', 'uid' => '$uid', 'class' => '$className', 'name' => '$name', 'parentProps' => $classArgs]);
            {$pkey} = new \\Ephect\\Modules\\Forms\\Components\\Children(\$struct);
            \$fn = \\$fqComponentName({$pkey}); \$fn(); 
            ?>
            PHP;

            $preg_opener = preg_quote($opener, '/');
            $preg_closer = preg_quote($closer, '/');

            preg_match('/(' . $preg_opener . ')/', $subject, $matches, PREG_OFFSET_CAPTURE);
            $startsAt = intval($matches[0][1]);
            $offset = $startsAt + strlen($opener) + strlen($componentBody);

            preg_match('/(' . $preg_closer . ')/', $subject, $matches, PREG_OFFSET_CAPTURE, $offset);
            $endsAt = intval($matches[0][1]);
            $length = $endsAt - $startsAt + strlen($closer);

            $outerComponentBody = substr($subject, $startsAt, $length);
            $subject = str_replace($outerComponentBody, $componentRender, $subject);

            $filename = $this->component->getSourceFilename();
            File::safeWrite($this->buildDirectory . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            $this->result[] = $componentName;

            $decl = ComponentDeclaration::byName($fqComponentName);
            $this->declareMiddlewares($motherUID, $parent, $decl, $fqComponentName, $props);
            //            $attributesEvent = new ComponentAttributesEvent($this->component, $item);
            //            $dispatcher = new EventDispatcher();
            //            $dispatcher->dispatch($attributesEvent);

            $previous = $item;
        };

        $closure($cmpz, 0);
        if ($cmpz->hasChildren()) {
            $parent = $cmpz;
            $cmpz->forEach($closure, $cmpz);
        }

        $this->html = $subject;
    }



    private function argumentsKeys(array $componentArgs): ?array
    {
        $result = [];

        foreach ($componentArgs as $key => $value) {
            $result[] = "\$" . $key;
        }

        return $result;
    }

    private function useArguments(array $argumentsKeys): ?string
    {
        $result = '';

        $args = implode(', ', $argumentsKeys);
        if ($args === '') {
            return ' ';
        }

        return " use (" . $args . ")";
    }
}
