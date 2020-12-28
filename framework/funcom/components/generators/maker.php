<?php

namespace FunCom\Components\Generators;

use FunCom\Components\AbstractComponent;
use FunCom\Components\ComponentInterface;
use FunCom\Components\Fragment;
use FunCom\Components\PreHtml;
use FunCom\ElementUtils;
use FunCom\IO\Utils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

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


    public function makeChildren(string $component, string $componentName, ?array $componentArgs, string $componentBody, string $componentBoundaries, ?string &$subject): bool
    {
        UseRegistry::uncache();
        ClassRegistry::uncache();
        ViewRegistry::uncache();

        $fqClass = UseRegistry::read($componentName);
        $filename = ClassRegistry::read($fqClass);
        $uid = ViewRegistry::read($filename);
        $namespace = ElementUtils::getNamespaceFromFQClassName($fqClass);

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
         * $componentRender = "<?php \FunCom\Components\View::make('$className', $classArgs, '$componentName'$args, $componentBoundaries, '$uid'); ?>";
         */

        $children = "['props' => $args, 'child' => ['name' => '$className', 'props' => $classArgs, 'uid' => '$uid']]";

        $componentRender = "<?php \$fn = $fqComponentName($children); \$fn(); ?>";

        //$componentRender = $this->makeFragment($componentName, $componentArgs, $uid);

        $subject = str_replace($component, $componentRender, $subject);

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
