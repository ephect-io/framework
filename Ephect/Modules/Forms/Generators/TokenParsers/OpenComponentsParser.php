<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentDeclarationStructure;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Registry\CodeRegistry;
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

        $subject = $this->html;

        $previous = null;

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
                $p = new ClosedComponentsParser($comp);
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
            $pkey = "\$children";
            if (count($propsKeys) === 1 && ($propsKeys[0] === "\$children" || $propsKeys[0] === "\$slot")) {
                $pkey = $propsKeys[0];
                $preComponentBody .= "\t\t\t<?php if(method_exists({$pkey}, 'props')) {\n";
                $preComponentBody .= "\t\t\t  \$props = {$pkey}->props();\n";
                $preComponentBody .= "\t\t\t  foreach(\$props as \$key => \$value) {\n";
                $preComponentBody .= "\t\t\t    $\$key = \$value;\n";
                $preComponentBody .= "\t\t\t  }\n";
                $preComponentBody .= "\t\t\t} ?>\n";
            }

            $componentRender = "<?php \$struct = new \\Ephect\\Modules\\Forms\\Components\\ChildrenStructure(['props' => (object) $props, 'buffer' => function()$useChildren{?>\n\n$preComponentBody$componentBody\n<?php\n}, 'motherUID' => '$motherUID', 'uid' => '$uid', 'class' => '$className', 'name' => '$name', 'parentProps' => $classArgs]);\n";
            $componentRender .= "\t\t\t{$pkey} = new \\Ephect\\Modules\\Forms\\Components\\Children(\$struct);\n";
            $componentRender .= "\t\t\t\$fn = \\$fqComponentName({$pkey}); \$fn(); ?>\n";

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
            File::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);
            File::safeWrite(STORE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            $this->result[] = $componentName;

            $list = CodeRegistry::read($fqComponentName);
            $struct = new ComponentDeclarationStructure($list);
            $decl = new ComponentDeclaration($struct);
            $hasAttrs = $decl->hasAttributes();

            Logger::create()->debug($hasAttrs ? $componentName . ' has attrs' : $componentName . ' nope', __FILE__, __LINE__);

            $this->declareMiddlewares($parent, $motherUID, $fqComponentName, $props, $hasAttrs);

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
