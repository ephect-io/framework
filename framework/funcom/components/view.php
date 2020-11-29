<?php

namespace FunCom\Components;

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

        list($this->namespace, $this->function, $this->code) = Analyser::getFunctionDefinition($this->filename);

        $result = $this->code !== false;

        return  $result;
    }



    public function render(): void
    {

        include $this->filename;

        $html = call_user_func($function);

        echo $html;
    }
}
