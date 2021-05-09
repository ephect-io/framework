<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentEntity;
use Ephect\Components\ComponentEntityInterface;
use Ephect\Registry\ComponentRegistry;

class ChildrenParser extends Parser
{
    /**
     * 
     * REGEX 101 https://regex101.com/r/BQRDmy/3 
     * 
     * @param null|string $subject 
     * @return null|object 
     */
    public function doChildrenDeclaration(?string $subject = null): ?object    {
        $result = null;
        $subject = $subject ?: $this->html;

        
        $re = '/(function([\w ]+)\(\$([\w]+)[^\)]*\)(\s|.)+?(\{))(\s|.)+?(\{\{ \3 \}\})/';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $functionDeclaration = $match[1];
            $componentName = $match[2];
            $variable = $match[7];

            $result = (object) ['declaration' => $functionDeclaration, 'component' => $componentName, 'variable'=>$variable];
        }

        return $result;
    }

    /**
     * Renders open components
     *
     * @return array
     */
    public function doOpenComponents(): array
    {
        $result = [];

        $comp = $this->component;
        $comp->resetDeclaration();
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if($cmpz === null || !$cmpz->hasCloser()) {
            return $result;
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
            $componentArgs = $item->props();

            $motherUID = $this->component->getMotherUID();
            $decl = $this->component->getDeclaration();
    
            $componentArgs = $componentArgs === null ? null : self::doArgumentsToString($componentArgs);
            $props = (($componentArgs === null) ? "null" : $componentArgs);
    
            $useChildren = $decl->hasArguments() ? " use (\$children) " : ' ';
    
            $className = $this->component->getFunction() ?: $componentName;
            $classArgs = '[]';
    
            $fqComponentName = '\\' . ComponentRegistry::read($componentName);
    
            $componentRender = "<?php \$struct = new \\Ephect\\Components\\ChildrenStructure(['props' => $props, 'onrender' => function()$useChildren{?>\n\n$componentBody\n<?php\n}, 'class' => '$className', 'parentProps' => $classArgs, 'motherUID' => '$motherUID']); ?>\n";
            $componentRender .= "\t\t\t<?php \$children = new \\Ephect\\Components\\Children(\$struct); ?>\n";
            $componentRender .= "\t\t\t<?php \$fn = $fqComponentName(\$children); \$fn(); ?>\n";
    
            $subject = str_replace($componentBody, $componentRender, $subject);
            $subject = str_replace($opener, '', $subject);
            $subject = str_replace($closer, '', $subject);

            array_push($result, $componentName);
        };

        $closure($cmpz, 0);
        if($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        } 

        $this->html = $subject;

        return $result;
    }

    public function doEntities(): array
    {
        $result = [];

        $entity = $this->component->getEntity();

        if($entity === null) {
            return $result;
        }
        
        if(!$entity->hasChildren()) {
            $this->doClosedEntity($entity);
        }

        if($entity->hasChildren()) {
            $this->doOpenEntity($entity);
        }

        return $result;
    }

    protected function doClosedEntity(ComponentEntityInterface $entity): void
    {
        $name = $entity->getName();
        $args = $entity->props();
    }

    protected function doOpenEntity(ComponentEntity $entity): void
    {
        # code...
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function doComponents(): array
    {
        $result = [];

        $comp = $this->component;
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if($cmpz === null) {
            return $result;
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

        return $result;
    }

    public function doFragments(): void
    {
        $this->html = str_replace('<>', '', $this->html);
        $this->html = str_replace('</>', '', $this->html);

    }

}
