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



    public static function renderHTML(string $functionName, ?array $functionArgs = null): string
    {

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


    public static function make(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        $html = parent::renderHTML($functionName, $functionArgs);

        $block = new Block($uid);

        $original = $block->getCode();
        $block->parse();

        $actual = $block->getCode();

        $html = str_replace($original, $actual, $html);

        echo $html;
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
