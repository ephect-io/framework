<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
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

        ClassRegistry::write($this->getFullCleasName(), $this->getSourceFilename());
        UseRegistry::safeWrite($this->getFunction(), $this->getFullCleasName());
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


    // public static function make(string $functionName, ?array $props, string $componentName, ?array $componentArgs = null, array $boundaries, string $uid): void
    // {
    //     $html = parent::renderComponent($componentName, $componentArgs);

    //     $fragment = new Fragment($uid, $html);

    //     $fragment->parse();

    //     $html = $fragment->getParentHTML();

    //     list($className, $cacheFilename) = static::findComponent($functionName);

    //     $prehtml = new PreHtml($html);
    //     $prehtml->load($cacheFilename);
    //     $prehtml->parse();

    //     $html = $prehtml->getCode();

    //     Utils::safeWrite(CACHE_DIR . $cacheFilename, $html);
    // }

    public static function bind(string $uid)
    {
        \FunCom\Registry\CodeRegistry::uncache();

        //$children = \FunCom\Registry\CodeRegistry::read($uid);

        $function = 'render_' . $uid;
        $filename = $function . '.php';

        include_once CACHE_DIR . $filename;

        $fn = call_user_func($function, $uid);
        $fn();
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
