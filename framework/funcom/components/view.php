<?php

namespace FunCom\Components;

use BadFunctionCallException;
use FunCom\IO\Utils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;
use tidy;

class View
{
    private $filename;
    private $namespace;
    private $function;
    private $code;

    public function getCode()
    {
        return $this->code;
    }

    public function getFullCleasName(): string
    {
        return $this->namespace  . '\\' . $this->function;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFunction(): string
    {
        return $this->function;
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
        list($this->namespace, $this->function, $this->code) = Parser::getFunctionDefinition(SRC_ROOT . $this->filename);
        $result = $this->code !== false;

        return  $result;
    }

    public function parse(): void
    {
        $parser = new Parser($this);
        $parser->doUses();
        $parser->doUsesAs();
        $parser->doVariables();
        $parser->doComponents();
        $html = $parser->getHtml();

        $this->html = $html;
       
        $this->cacheHtml();

        ClassRegistry::write($this->getFullCleasName(), $this->getCacheFilename());
        UseRegistry::safeWrite($this->getFunction(), $this->getFullCleasName());
    }

    private function cacheHtml(): ?string
    {
        $cache_file = $this->getCacheFilename();

        $result = Utils::safeWrite(SITE_ROOT . $cache_file, $this->html);

        return $result === null ? $result : $cache_file;
    }

    public static function render(string $functionName, ?array $functionArgs = null): void
    {
        ClassRegistry::uncache();
        UseRegistry::uncache();

        $classes =  ClassRegistry::items();
        $uses =  UseRegistry::items();

        $functionName = isset($uses[$functionName]) ? $uses[$functionName] : null;
        if ($functionName === null) {
            throw new BadFunctionCallException('The function ' . $functionName . ' does not exist.');
        }

        $cacheFilename = isset($classes[$functionName]) ? $classes[$functionName] : null;

        include SITE_ROOT . $cacheFilename;

        $html = '';
        if ($functionArgs === null) {
            $html = call_user_func($functionName);
        }

        if ($functionArgs !== null) {
            
            $props = [];
            foreach ($functionArgs as $key => $value) {
                $props[$key] = urldecode($value);
            }

            $props = (object) $props;

            $html = call_user_func($functionName, $props);
        }

        ob_start();
        eval('?>' . $html);
        $html = ob_get_clean();

        $fqFunctionName = explode('\\', $functionName);
        $function = array_pop($fqFunctionName);
        if ($function === 'App') {
            $html = self::format($html);
        }

        echo $html;
    }

    public static function format(string $html): string
    {
        $config = [
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 200
        ];

        $tidy = new tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy->value;
    }


}
