<?php

namespace FunCom\Components;

use BadFunctionCallException;
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

    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;
        list($this->namespace, $this->function, $this->code) = Parser::getFunctionDefinition(SRC_ROOT . $this->filename);
        $result = $this->code !== false;

        return  $result;
    }

    public static function render(string $functionName, ?string $functionArgs = null): void
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
            $args = json_decode($functionArgs, JSON_OBJECT_AS_ARRAY);

            $props = [];
            foreach ($args as $key => $value) {
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
