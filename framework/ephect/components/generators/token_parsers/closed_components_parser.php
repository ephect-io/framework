<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\ComponentEntityInterface;
use Ephect\Registry\ComponentRegistry;

final class ClosedComponentsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $comp = $this->component;
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if($cmpz === null) {
            return;
        }

        $str = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index)  use (&$str, &$result) {
            $args = '';

            if($item->hasCloser()) {
                return;
            }

            $component = $item->getText();
            $componentName = $item->getName();

            $componentArgs = $item->props();

            $funcName = ComponentRegistry::read($componentName);

            if ($componentArgs !== null) {
                $args = json_encode($componentArgs);
                $args = "json_decode('$args')";
            }

            $componentRender = "\t\t\t<?php \$fn = \\${funcName}($args); \$fn(); ?>\n";

            $str = str_replace($component, $componentRender, $str);

            array_push($result, $componentName);
        };

        if (!$cmpz->hasChildren()) 
        {
            $closure($cmpz, 0);
        } 
        if($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        } 

        $this->html = $str;

    }
    
}