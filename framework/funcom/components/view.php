<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;

class View extends AbstractFileComponent
{

    public function __construct(string $uid = '')
    {
        $this->uid = $uid;
        $this->getUID();
    }

    public function analyse(): void
    {
        parent::analyse();

        ClassRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        UseRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }

    private function cacheHtml(): ?string
    {
        $cache_file = static::getCacheFilename($this->filename);
        $result = Utils::safeWrite(CACHE_DIR . $cache_file, $this->code);

        return $result === null ? $result : $cache_file;
    }

    public static function renderHTML(string $functionName, ?array $functionArgs = null): string
    {

        parent::renderComponent($functionName, $functionArgs);

        $html = parent::renderHTML($functionName, $functionArgs);

        return $html;
    }

    public static function render(string $functionName, ?array $functionArgs = null): void
    {
        $html =  self::renderHTML($functionName, $functionArgs);

        echo $html;
    }


    public static function make(string $parentComponent, string $functionName, ?array $props, string $componentName, ?array $componentArgs = null, array $boundaries, string $uid): void
    {
        list($namespace, $className, $html) = parent::renderComponent($parentComponent, $componentArgs);

        $fragment = new Fragment($uid, $html);

        $fragment->parse();

        $html = $fragment->getParentHTML();

        list($className, $cacheFilename) = static::findComponent($functionName);

        $prehtml = new PreHtml($html);
        $prehtml->load($cacheFilename);
        $prehtml->parse();

        $html = $prehtml->getCode();

        Utils::safeWrite(CACHE_DIR . $cacheFilename, $html);
    }

    public static function bind(string $uid)
    {
        CodeRegistry::uncache();
        
        $body = CodeRegistry::read($uid);
        $body = urldecode($body);

        $prehtml = new PreHtml($body);
        $prehtml->parse();

        $html = $prehtml->getCode();

        eval('?>' . $html);

    }

    public static function replace(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        if ($functionName === 'Block') {
            echo '';
            return;
        }

        list($functionName, $cacheFilename) = self::findComponent($functionName);


        // TO BE DONE
        $html = '';

        echo $html;
    }
}
