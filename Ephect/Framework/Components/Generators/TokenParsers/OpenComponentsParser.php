<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\WebComponents\ManifestReader;

final class OpenComponentsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
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

        // $parentProps = $cmpz->props();

        $subject = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index)  use (&$subject, &$result) {

            if (!$item->hasCloser()) {
                return;
            }
            $uid = $item->getUID();

            $opener = $item->getText();
            $closer = ((object) $item->getCloser())->text;
            $componentName = $item->getName();
            // $componentBody = $item->getContents($subject);
            $componentBody = $item->getInnerHTML();
            $componentArgs = $this->useVariables;
            $componentArgs = $item->props() !== null ? array_merge($componentArgs, $item->props()) : $componentArgs;

            if ($componentName === 'FakeFragment') {
                return;
            }

            if ($componentName === 'Fragment') {
                return;
            }

            if ($componentName === 'Slot') {
                return;
            }

            $motherUID = $this->component->getMotherUID();
            $decl = $this->component->getDeclaration();

            $propsArgs = $componentArgs === null ?: self::doArgumentsToString($componentArgs);
            $props = (($propsArgs === null) ? "[]" : $propsArgs);

            $propsKeys = $this->argumentsKeys($this->useVariables);

            $useChildren = $decl->hasArguments() || count($propsKeys) ? $this->useArguments($propsKeys) : ' ';

            $className = $this->component->getFullyQualifiedFunction() ?: $componentName;
            $name = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]'; //'json_decode("' . json_encode($parentProps) . '")';

            $fqComponentName = ComponentRegistry::read($componentName);
            $filename = ComponentRegistry::read($fqComponentName);

            $wopener = '';
            $wcloser = '';
            if ($filename === null) {
                $filename = WebComponentRegistry::read($fqComponentName);
                if ($filename !== null) {
                    // $uid = WebComponentRegistry::read($filename);
                    $reader = new ManifestReader($motherUID, $componentName);
                    $manifest = $reader->read();
                    $tag = $manifest->getTag();
                    $text = str_replace($componentName, $tag, $opener . $componentBody . $closer);
                    // $wopener = str_replace($componentName, $tag, $opener);
                    // $wcloser = str_replace($componentName, $tag, $closer);
                    Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $componentName . $uid . '.txt', $text);
                }
            }

            $preComponentBody = '';
            if (count($propsKeys) === 1 && $propsKeys[0] === "\$children") {
                $preComponentBody .= "\t\t\t<?php \$props = \$children->props(); ?>\n";
                $preComponentBody .= "\t\t\t<?php foreach(\$props as \$key => \$value) { ?>\n";
                $preComponentBody .= "\t\t\t<?php     $\$key = \"\$value\"; ?>\n";
                $preComponentBody .= "\t\t\t<?php } ?>\n";
            }

            $componentRender = "<?php \$struct = new \\Ephect\\Framework\\Components\\ChildrenStructure(['props' => (object) $props, 'buffer' => function()$useChildren{?>\n\n$preComponentBody$wopener$componentBody$wcloser\n<?php\n}, 'uid' => '$uid', 'motherUID' => '$motherUID', 'class' => '$className', 'name' => '$name', 'parentProps' => $classArgs]); ?>\n";
            $componentRender .= "\t\t\t<?php \$children = new \\Ephect\\Framework\\Components\\Children(\$struct); ?>\n";
            $componentRender .= "\t\t\t<?php \$fn = \\$fqComponentName(\$children); \$fn(); ?>\n";

            $subject = str_replace($componentBody, $componentRender, $subject);

            $opener = preg_quote($opener, '/');
            $subject = preg_replace('/(' . $opener . ')/su', '', $subject, 1);

            $filename = $this->component->getFlattenSourceFilename();
            Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            $closer = preg_quote($closer, '/');
            $subject = preg_replace('/' . $closer . '(?!.*' . $closer . ')/su', '', $subject, 1);

            $filename = $this->component->getFlattenSourceFilename();
            Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            $this->result[] = $componentName;
        };

        $closure($cmpz, 0);
        if ($cmpz->hasChildren()) {
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
}
