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
            $componentArgs = $this->useVariables; //$item->props();


            if($componentName === 'Fragment') {
                return;
            }

            if($componentName === 'Block') {
                return;
            }

            $motherUID = $this->component->getMotherUID();
            $decl = $this->component->getDeclaration();
    
            $propsArgs = $componentArgs === null ? null : self::doArgumentsToString($componentArgs);
            $props = (($propsArgs === null) ? "null" : $propsArgs);
    
            $useChildren = $decl->hasArguments() ? $this->useArguments($componentArgs) : ' ';
    
            $className = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]';
    
            $fqComponentName = '\\' . ComponentRegistry::read($componentName);
    
            $componentRender = "<?php \$struct = new \\Ephect\\Components\\ChildrenStructure(['props' => $props, 'onrender' => function()$useChildren{?>\n\n$componentBody\n<?php\n}, 'class' => '$className', 'parentProps' => $classArgs, 'motherUID' => '$motherUID']); ?>\n";
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
    
    private function useArguments(array $componentArgs): ?string
    {
        $result = '';

        $keys = [];

        foreach ($componentArgs as $key => $value) {
            array_push($keys, "\$" . $key);
        }

        $result = " use (" . implode(', ', $keys) . ")";

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