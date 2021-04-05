<?php

namespace Ephect\Components;

use BadFunctionCallException;
use Ephect\Components\Generators\ChildrenParser;
use Ephect\Components\Generators\Parser;
use Ephect\ElementTrait;
use Ephect\IO\Utils;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\Tree;
use tidy;

abstract class AbstractComponent extends Tree implements ComponentInterface
{
    use ElementTrait;

    protected $function = null;
    protected $code;
    protected $parentHTML;
    protected $componentList = [];
    protected $children = null;

    public function getParentHTML(): ?string
    {
        return $this->parentHTML;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getFullyQualifiedFunction(): string
    {
        return $this->namespace  . '\\' . $this->function;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function analyse(): void
    {
        $parser = new Parser($this);
        $parser->doUses();
        $parser->doUsesAs();
    }

    public function compose(): void
    {
        $composition = CodeRegistry::read($this->getFullyQualifiedFunction());
        $entity = ComponentEntity::buildFromArray($composition);
        $this->add($entity);
    }

    public function composedOf(): ?array
    {
        $names = [];

        $this->forEach(function (ComponentEntityInterface $item, $key) use (&$names) {
            $funcName = $item->getName();
            $fqFuncName = ComponentRegistry::read($funcName);
            $names[$funcName] = $fqFuncName;
        }, $this);

        $names = array_unique($names);

        $names = array_filter($names, function ($item) {
            return $item !== null;
        });

        if (count($names) === 0) {
            $names = null;
        }

        return $names;
    }



    public function parse(): void
    {
        /* TO BEGIN WITH */
        // CodeRegistry::uncache();
        // $class = $this->getFullyQualifiedFunction();
        // $item = CodeRegistry::read($class);
        /* TO BEGIN WITH */

        $parser = new ChildrenParser($this);

        $parser->doUncache();
        $parser->doPhpTags();

        $this->children = $parser->doChildrenDeclaration();
        $parser->doValues();
        $parser->doEchoes();
        $parser->doArrays();
        $parser->useVariables();
        $parser->normalizeNamespace();
        $parser->doFragments();
        $componentList = $parser->doComponents();
        $openComponentList = $parser->doOpenComponents();

        $this->componentList = array_unique(array_merge($componentList, $openComponentList));

        $html = $parser->getHtml();

        $parser->doCache();

        $this->code = $html;
    }

    public static function findComponent(string $componentName, string $motherUID): array
    {
        ComponentRegistry::uncache();
        $uses = ComponentRegistry::items();
        $fqFuncName = isset($uses[$componentName]) ? $uses[$componentName] : null;

        if ($fqFuncName === null) {
            throw new BadFunctionCallException('The component ' . $componentName . ' does not exist.');
        }

        CacheRegistry::uncache();

        if ($motherUID === '') {
            $filename = $uses[$fqFuncName];
            $motherUID = $uses[$filename];
        }
        $filename = CacheRegistry::read($motherUID, $fqFuncName);
        $filename = ($filename !== null) ? $motherUID . DIRECTORY_SEPARATOR . $filename : $filename;
        $isCached = $filename !== null;

        return [$fqFuncName, $filename, $isCached];
    }

    public static function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null): string
    {

        include_once CACHE_DIR . $cacheFilename;

        $html = '';
        if ($functionArgs === null) {
            ob_start();
            $fn = call_user_func($fqFunctionName);
            $fn();
            $html = ob_get_clean();
        }

        if ($functionArgs !== null) {

            $props = [];
            foreach ($functionArgs as $key => $value) {
                $props[$key] = urldecode($value);
            }

            $props = (object) $props;

            ob_start();
            $fn = call_user_func($fqFunctionName, $props);
            $fn();
            $html = ob_get_clean();
        }

        // $fqFunctionName = explode('\\', $functionName);
        // $function = array_pop($fqFunctionName);
        // if ($function === 'App') {
        //     $html = self::format($html);
        // }

        return $html;
    }

    public static function functionName($fullQualifiedName): string
    {
        $fqFunctionName = explode('\\', $fullQualifiedName);
        $function = array_pop($fqFunctionName);

        return $function;
    }

    public static function passChidren(array $children): array
    {
        $componentProps = $children["props"];
        $childProps = $children["child"]["props"];
        $props = is_array($componentProps) && is_array($childProps) ? array_merge($componentProps, $childProps) : $componentProps;
        $props = !is_array($componentProps) && is_array($childProps) ? $childProps : $componentProps;

        $child = $children["child"]["name"];
        $uid = $children["child"]["uid"];

        return [$props, $uid];
    }

    public static function format(string $html): string
    {
        $config = [
            'indent'         => true,
            'output-html'   => true,
            'wrap'           => 200
        ];

        $tidy = new tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy->value;
    }
}
