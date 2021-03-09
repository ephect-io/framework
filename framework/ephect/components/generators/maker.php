<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentFactory;
use Ephect\Components\ComponentInterface;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;

class Maker
{
    private $html = '';
    private $comp = null;
    private $parentHTML = '';

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->html = $comp->getCode();
        $this->parentHTML = $comp->getParentHTML();
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function doCache(): bool
    {
        return CodeRegistry::cache();
    }

    public function doUncache(): bool
    {
        return CodeRegistry::uncache();
    }


    public function makeChildren(string $componentText, string $componentName, ?array $componentArgs, string $componentBody, string $componentBoundaries, ?string &$subject): bool
    {
        ComponentRegistry::uncache();

        $fqClass = ComponentRegistry::read($componentName);
        $component = ComponentFactory::create($fqClass);

        $uid = $component->getUID();
        $namespace = $component->getNamespace();

        $args = $componentArgs === null ? null : $this->doArgumentsToString($componentArgs);
        $args = (($args === null) ? "null" : $args);

        $children = <<<CHILDREN
        <?php
        
        namespace $namespace;

        ?>
        $componentBody
        
        CHILDREN;

        // Utils::safeWrite(CACHE_DIR . "render_$uid.php", $children);
        $body = urlencode($children);
        CodeRegistry::write($uid, $body);

        $className = $this->component->getFunction() ?: $componentName;
        $classArgs = 'null';

        $fqComponentName = '\\' . ComponentRegistry::read($componentName);

        $children = "['props' => $args, 'child' => ['name' => '$className', 'props' => $classArgs, 'uid' => '$uid']]";

        $componentRender = "<?php \$fn = $fqComponentName($children); \$fn(); ?>";

        $subject = str_replace($componentText, $componentRender, $subject);

        $result = $subject !== null;

        return $result;
    }

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {

            $result .= '"' . $key . '" => "' . urlencode($value) . '", ';
        }
        $result = ($result === '') ? null : '[' . $result . ']';

        return $result;
    }

}
