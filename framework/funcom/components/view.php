<?php

namespace FunCom\Components;

use BadFunctionCallException;
use BadMethodCallException;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

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
            throw new BadFunctionCallException('The functiin ' . $functionName . ' does not exist.');
        }

        $cacheFilename = isset($classes[$functionName]) ? $classes[$functionName] : null;

        include SITE_ROOT . $cacheFilename;

        $html = '';
        if($functionArgs === null) {
            $html = call_user_func($functionName);
        }

        if($functionArgs !== null) {
            $args = json_decode($functionArgs);
            $html = call_user_func($functionName, $args);
        }
     
        eval('?>' . $html);

        //echo $html;
    }
}
