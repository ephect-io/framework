<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\ComponentEntityInterface;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

final class ClosedComponentsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $this->result = [];

        $comp = $this->component;
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if($cmpz === null) {
            return;
        }

        $subject = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index)  use (&$subject, &$result) {

            if($item->hasCloser()) {
                return;
            }
            
            $component = $item->getText();
            $componentName = $item->getName();
            $componentArgs = $item->props();

            $args = '';
            if ($componentArgs !== null) {
                $args = json_encode($componentArgs, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
                $args = "json_decode('$args')";
            }

            $funcName = ComponentRegistry::read($componentName);
            $componentRender = "\t\t\t<?php \$fn = \\${funcName}($args); \$fn(); ?>\n";

            $subject = str_replace($component, $componentRender, $subject);

            array_push($this->result, $componentName);

            $filename = $this->component->getFlattenSourceFilename();
            Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);

        };

        if (!$cmpz->hasChildren()) 
        {
            $closure($cmpz, 0);
        } 
        if($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        } 

        $this->html = $subject;

    }
    
}