<?php

namespace FunCom\Components;

use BadFunctionCallException;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;
use tidy;

abstract class AbstractComponent implements ComponentInterface
{
    protected $filename;
    protected $namespace;
    protected $function;
    protected $code;


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

    public static function findComponent(string $componentName): array
    {
        CacheRegistry::uncache();
        UseRegistry::uncache();

        $classes =  CacheRegistry::items();
        $uses =  UseRegistry::items();

        $functionName = isset($uses[$componentName]) ? $uses[$componentName] : null;
        if ($functionName === null) {
            throw new BadFunctionCallException('The function ' . $functionName . ' does not exist.');
        }

        $cacheFilename = isset($classes[$functionName]) ? $classes[$functionName] : null;

        return [$functionName, $cacheFilename];
    }

    public function analyse(): void
    {
        $parser = new Parser($this);
        $parser->doUses();
        $parser->doUsesAs();
    }

    public function parse(): void
    {
        $parser = new Parser($this);
        $parser->doVariables();
        $parser->useVariables();
        $parser->doComponents();
        $parser->doOpenComponents();
        $html = $parser->getHtml();

        $this->code = $html;
    }

    public function getFunctionDefinition(): ?array
    {
        $contents = $this->code;

        if ($contents === null) {
            return null;
        }

        $namespace = $this->grabKeywordName('namespace', $contents, ';');
        $functionName = $this->grabKeywordName('function', $contents, '(');

        return [$namespace, $functionName];
    }

    public function getClassDefinition(): ?array
    {
        $contents = $this->code;

        if ($contents === null) {
            return null;
        }

        $namespace = $this->grabKeywordName('namespace', $contents, ';');
        $className = $this->grabKeywordName('class', $contents, ' ');

        return [$namespace, $className];
    }

    public function grabKeywordName(string $keyword, string $classText, string $delimiter): string
    {
        $result = '';

        $start = strpos($classText, $keyword);
        if ($start > -1) {
            $start += \strlen($keyword) + 1;
            $end = strpos($classText, $delimiter, $start);
            $result = substr($classText, $start, $end - $start);
        }

        return $result;
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
