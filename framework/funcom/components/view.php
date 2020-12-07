<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

class View extends AbstractComponent
{

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public function getCacheFilename(): string
    {
        $cache_file = REL_CACHE_DIR . str_replace('/', '_', $this->filename);

        return $cache_file;
    }

    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(SRC_ROOT . $this->filename);

        list($this->namespace, $this->function) = $this->getFunctionDefinition();
        $result = $this->code !== false;

        return  $result;
    }

    public function analyse(): void
    {
        parent::analyse();

        $this->cacheHtml();

        ClassRegistry::write($this->getFullCleasName(), $this->getSourceFilename());
        CacheRegistry::write($this->getFullCleasName(), $this->getCacheFilename());
        UseRegistry::safeWrite($this->getFunction(), $this->getFullCleasName());
    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }

    private function cacheHtml(): ?string
    {
        $cache_file = $this->getCacheFilename();
        $result = Utils::safeWrite(SITE_ROOT . $cache_file, $this->code);

        return $result === null ? $result : $cache_file;
    }


    public static function importComponent(string $componentName): string
    {
        list($functionName, $cacheFilename) = static::findComponent($componentName);

        include SITE_ROOT . $cacheFilename;

        return $functionName;
    }


    public static function render(string $functionName, ?array $functionArgs = null): void
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
            $html = self::format($html);
        }

        echo $html;
    }


    public static function make(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        list($functionName, $cacheFilename) = self::findComponent($functionName);

        $block = new Block($uid);
        $block->parse();

        $html = $block->getCode();

        echo $html;
    }

    public static function replace(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        list($functionName, $cacheFilename) = self::findComponent($functionName);


        // TO BE DONE
        $html = '';

        echo $html;
    }
}
