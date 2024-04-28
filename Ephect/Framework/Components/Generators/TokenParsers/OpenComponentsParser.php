<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Framework\WebComponents\ManifestReader;

final class OpenComponentsParser extends AbstractTokenParser
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
        $parent = null;
        $closure = function (ComponentEntityInterface $item, int $index) use ($comp, &$subject, &$result, &$previous, &$parent) {
            $parent = $previous != null && $previous->getDepth() < $item->getDepth() ? $previous : null;

            if (!$item->hasCloser()) {
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

            $propsArgs = self::doArgumentsToString($componentArgs);
            $props = (($propsArgs === null) ? "[]" : $propsArgs);

            $propsKeys = $this->argumentsKeys($this->useVariables);

            $useChildren = $decl->hasArguments() || count($propsKeys) ? $this->useArguments($propsKeys) : ' ';

            $className = $this->component->getFullyQualifiedFunction() ?: $componentName;
            $name = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]';

            $fqComponentName = ComponentRegistry::read($componentName);
            $filename = ComponentRegistry::read($fqComponentName);

            if ($filename === null) {
                $filename = WebComponentRegistry::read($fqComponentName);
                if ($filename !== null) {
                    $reader = new ManifestReader($motherUID, $componentName);
                    $manifest = $reader->read();
                    $tag = $manifest->getTag();
                    $wcom = str_replace($componentName, $tag, $opener . $componentBody . $closer);
                    File::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $componentName . $uid . '.txt', $wcom);
                }
            }

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

            $componentRender = "<?php \$struct = new \\Ephect\\Framework\\Components\\ChildrenStructure(['props' => (object) $props, 'buffer' => function()$useChildren{?>\n\n$preComponentBody$componentBody\n<?php\n}, 'motherUID' => '$motherUID', 'uid' => '$uid', 'class' => '$className', 'name' => '$name', 'parentProps' => $classArgs]);\n";
            $componentRender .= "\t\t\t{$pkey} = new \\Ephect\\Framework\\Components\\Children(\$struct);\n";
            $componentRender .= "\t\t\t\$fn = \\$fqComponentName({$pkey}); \$fn(); ?>\n";

            $preg_opener = preg_quote($opener, '/');
            $preg_closer = preg_quote($closer, '/');

            preg_match('/(' . $preg_opener . ')/', $subject, $matches, PREG_OFFSET_CAPTURE);
            $startsAt = intval($matches[0][1]);
            preg_match('/(?s:.*\s)?\K' . $preg_closer . '(?!.*' . $preg_closer . ')/', $subject, $matches, PREG_OFFSET_CAPTURE);
            $endsAt = intval($matches[0][1]);

            $length = $endsAt - $startsAt + strlen($closer);

            $outerComponentBody = substr($subject, $startsAt, $length);

            $subject = str_replace($outerComponentBody, $componentRender, $subject);

            $filename = $this->component->getFlattenSourceFilename();
            File::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            $this->result[] = $componentName;

            $previous = $item;

        };

        $closure($cmpz, 0);
        if ($cmpz->hasChildren()) {
            $parent = $cmpz;
            $cmpz->forEach($closure, $cmpz);
        }

        $this->html = $subject;
    }

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $pair = '"' . $key . '" => "' . urlencode($value) . '", ';
            if ($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';
            }
            $result .= $pair;
        }
        return ($result === '') ? null : '[' . $result . ']';
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
