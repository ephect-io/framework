<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\ComponentEntityInterface;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

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

        if($cmpz === null || !$cmpz->hasCloser()) {
            return;
        }

        $subject = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index)  use (&$subject, &$result) {

            if(!$item->hasCloser()) {
                return;
            }

            $opener = $item->getText();
            $closer = ((object) $item->getCloser())->text;
            $componentName = $item->getName();
            $componentBody = $item->getContents($subject);
            $componentArgs = $this->useVariables;
            $componentArgs = $item->props() !== null ? array_merge($componentArgs, $item->props()) : $componentArgs;

            if($componentName === 'Fragment') {
                return;
            }

            if($componentName === 'Slot') {
                return;
            }

            $motherUID = $this->component->getMotherUID();
            $decl = $this->component->getDeclaration();
    
            $propsArgs = $componentArgs === null ? null : self::doArgumentsToString($componentArgs);
            $props = (($propsArgs === null) ? "[]" : $propsArgs);

            $propsKeys = $this->argumentsKeys($this->useVariables);
            
            $useChildren = $decl->hasArguments() ? $this->useArguments($propsKeys) : ' ';
    
            $className = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]';
    
            $fqComponentName = '\\' . ComponentRegistry::read($componentName);
    
            $preComponentBody = '';
            if(count($propsKeys) === 1 && $propsKeys[0] === "\$children") {
                $preComponentBody .= "\t\t\t<?php \$props = \$children->props(); ?>\n";
                $preComponentBody .= "\t\t\t<?php foreach(\$props as \$key => \$value) { ?>\n";
                $preComponentBody .= "\t\t\t<?php     $\$key = \"\$value\"; ?>\n";
                $preComponentBody .= "\t\t\t<?php } ?>\n";
            }
            
            $componentRender = "<?php \$struct = new \\Ephect\\Components\\ChildrenStructure(['props' => (object) $props, 'onrender' => function()$useChildren{?>\n\n$preComponentBody$componentBody\n<?php\n}, 'class' => '$className', 'parentProps' => $classArgs, 'motherUID' => '$motherUID']); ?>\n";
            $componentRender .= "\t\t\t<?php \$children = new \\Ephect\\Components\\Children(\$struct); ?>\n";
            $componentRender .= "\t\t\t<?php \$fn = $fqComponentName(\$children); \$fn(); ?>\n";

            $subject = str_replace($componentBody, $componentRender, $subject);
            $subject = str_replace($opener, '', $subject);
            $subject = str_replace($closer, '', $subject);

            $filename = $this->component->getFlattenSourceFilename();
            Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

            array_push($this->result, $componentName);

        };

        $closure($cmpz, 0);
        if($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        } 

        $this->html = $subject;
    }
    
    private function argumentsKeys(array $componentArgs): ?array
    {
        $result = [];

        foreach ($componentArgs as $key => $value) {
            array_push($result, "\$" . $key);
        }

        return $result;
    }

    private function useArguments(array $argumentsKeys): ?string
    {
        $result = '';

        $args = implode(', ', $argumentsKeys);
        if($args === '') {
            return ' ';
        }

        $result = " use (" . $args . ")";
     
        return $result;

    }

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $pair = '"' . $key . '" => "' . urlencode($value) . '", ';
            if($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';

            }
            $result .= $pair;
        }
        $result = ($result === '') ? null : '[' . $result . ']';

        return $result;
    }
}