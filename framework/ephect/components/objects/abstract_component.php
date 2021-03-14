<?php

namespace Ephect\Components;

use BadFunctionCallException;
use Ephect\Components\Generators\ChildrenParser;
use Ephect\Components\Generators\Parser;
use Ephect\ElementTrait;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use tidy;

abstract class AbstractComponent implements ComponentInterface
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

    public function getCode()
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
        $parser->doComponents();
        $this->componentList = $parser->doOpenComponents();
        $html = $parser->getHtml();

        $parser->doCache();

        $this->code = $html;
    }


    public static function checkCache(string $componentName): bool
    {
        list($functionName, $cacheFilename, $isCached) = static::findComponent($componentName);

        return $isCached;
    }

    public static function findComponent(string $componentName): array
    {
        ComponentRegistry::uncache();
        $uses = ComponentRegistry::items();

        $functionName = isset($uses[$componentName]) ? $uses[$componentName] : null;
        if ($functionName === null) {
            throw new BadFunctionCallException('The component ' . $componentName . ' does not exist.');
        }

        CacheRegistry::uncache();
        $cache = CacheRegistry::items();
        $filename = isset($cache[$functionName]) ? $cache[$functionName] : null;
        $isCached = $filename !== null;

        return [$functionName, $filename, $isCached];
    }


    public static function importComponent(string $componentName): ?string
    {
        list($functionName, $cacheFilename, $isCached) = static::findComponent($componentName);

        include_once CACHE_DIR . $cacheFilename;

        return $functionName;
    }

    public static function renderHTML(string $functionName, ?array $functionArgs = null): string
    {
        $functionName = self::importComponent($functionName);

        $html = '';
        if ($functionArgs === null) {
            ob_start();
            $fn = call_user_func($functionName);
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
            $fn = call_user_func($functionName, $props);
            $fn();
            $html = ob_get_clean();
        }

        $fqFunctionName = explode('\\', $functionName);
        $function = array_pop($fqFunctionName);
        if ($function === 'App') {
            // $html = self::format($html);
        }

        return $html;
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
