<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentFactory;
use Ephect\Components\ComponentInterface;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\UseRegistry;
use Ephect\Registry\ViewRegistry;

class Maker
{
    private $html = '';
    private $view = null;
    private $parentHTML = '';

    public function __construct(ComponentInterface $view)
    {
        $this->view = $view;
        $this->html = $view->getCode();
        $this->parentHTML = $view->getParentHTML();
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
        UseRegistry::uncache();
        ViewRegistry::uncache();

        $fqClass = UseRegistry::read($componentName);
        $component = ComponentFactory::create($fqClass);

        $uid = $component->getUID();
        $namespace = $component->getNamespace();

        $args = $this->doArgumentsToString($componentArgs);
        $args = (($args === null) ? "null" : $args);

        $children = <<<CHILDREN
        <?php
        
        namespace $namespace;

        ?>
        $componentBody
        <?php
        CHILDREN;

        // Utils::safeWrite(CACHE_DIR . "render_$uid.php", $children);
        $body = urlencode($children);
        CodeRegistry::write($uid, $body);

        $className = $this->view->getFunction() ?: $componentName;
        $classArgs = 'null';

        $fqComponentName = '\\' . UseRegistry::read($componentName);


        /**
         * $componentRender = "<?php \Ephect\Components\View::make('$className', $classArgs, '$componentName'$args, $componentBoundaries, '$uid'); ?>";
         */

        $children = "['props' => $args, 'child' => ['name' => '$className', 'props' => $classArgs, 'uid' => '$uid']]";

        $componentRender = "<?php \$fn = $fqComponentName($children); \$fn(); ?>";

        //$componentRender = $this->makeFragment($componentName, $componentArgs, $uid);

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
