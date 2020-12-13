<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

class View extends AbstractFileComponent
{

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

        if(!static::checkCache($functionName)) {
            ClassRegistry::uncache();

            $fqName = UseRegistry::read($functionName);
            $filename = ClassRegistry::read($fqName);
            $view = new View();
            $view->load($filename);
            $view->parse();
            
            CacheRegistry::write($view->getFullCleasName(), static::getCacheFilename($view->getSourceFilename()));
            CacheRegistry::cache();
        }

        $html = parent::renderHTML($functionName, $functionArgs);

        $fqFunctionName = explode('\\', $functionName);
        $function = array_pop($fqFunctionName);
        if ($function === 'App') {
            $html = self::format($html);
        }

        return $html;
    }

    public static function render(string $functionName, ?array $functionArgs = null): void
    {
        $html =  self::renderHTML($functionName, $functionArgs);

        echo $html;
    }


    public static function make(string $functionName, ?array $props, string $componentName, ?array $componentArgs = null, array $boundaries, string $uid): void
    {
        $html = self::renderHTML($componentName, $componentArgs);

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

    public static function replace(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        if($functionName === 'Block') 
        {
            echo '';
            return;
        }

        list($functionName, $cacheFilename) = self::findComponent($functionName);


        // TO BE DONE
        $html = '';

        echo $html;
    }
}
