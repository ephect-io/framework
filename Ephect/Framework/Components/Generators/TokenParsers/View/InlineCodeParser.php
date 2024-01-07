<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class InlineCodeParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $phtml = [];

        $text = $this->html;
        if($parameter !== null && is_array($parameter)) {
            $text = $parameter['html'];
            $this->useVariables = $parameter['useVariables'];
        }

        $lines = explode(PHP_EOL, $text);

        foreach($lines as $line) {
            $line = $this->doIf($line);
            $line = $this->doElseIf($line);
            $line = $this->doElse($line);
            $line = $this->doForeach($line);
            $line = $this->doFor($line);
            $line = $this->doWhile($line);
            $line = $this->doDo($line);
            $line = $this->doDoWhile($line);
            $line = $this->doBreaker($line);
            $line = $this->doEnd($line);
            $line = $this->doOperation($line);

            $phtml[] = $line;
        }

        $text = implode(PHP_EOL, $phtml);

        $text = $this->doPhpTags($text);

        $text = $this->doEchoes($text);
        $text = $this->doValues($text);
        $this->doVariables();

        $text = $this->doPhpCleaner($text);

        $this->result = $text;
    }

    public function doIf(string $html): string
    {
        $parser = new IfParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doElseIf(string $html): string
    {
        $parser = new ElseIfParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doElse(string $html): string
    {
        $parser = new ElseParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doEnd(string $html): string
    {
        $parser = new EndParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doFor(string $html): string
    {
        $parser = new ForParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doForeach(string $html): string
    {
        $parser = new ForeachParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doWhile(string $html): string
    {
        $parser = new WhileParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doDo(string $html): string
    {
        $parser = new DoParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doDoWhile(string $html): string
    {
        $parser = new DoWhileParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doOperation(string $html): string
    {
        $parser = new OperationParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doBreaker(string $html): string
    {
        $parser = new BreakerParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doValues(string $html): string
    {
        $parser = new ValuesParser($this->component);
        $parser->do([
            "html" => $html,
            "useVariables" => $this->useVariables,
        ]);

        $this->useVariables = $parser->getUseVariables();
        return $parser->getResult();
    }

    public function doVariables(): void
    {
        $parser = new VariablesParser($this->component);
        $parser->do([
            "useVariables" => $this->useVariables,
        ]);

        $this->funcVariables = $parser->getFuncVariables();
        $this->useVariables = $parser->getUseVariables();
    }

    public function doPhpTags(string $html): string
    {
        $parser = new PhpTagsParser($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doPhpCleaner(string $html): string
    {
        $parser = new PhpTagsCleaner($this->component);
        $parser->do($html);
        return $parser->getResult();
    }

    public function doEchoes(string $html): string
    {
        $parser = new EchoParser($this->component);
        $parser->do([
            "html" => $html,
            "useVariables" => $this->useVariables,
        ]);
        $this->useVariables = $parser->getUseVariables();

        return $parser->getResult();
    }
}
