<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;
use Ephect\Framework\Components\Generators\TokenParsers\HtmlParser;

final class InlineCodeParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $phtml = [];

        $lines = explode(PHP_EOL, $this->html);

        foreach($lines as $line) {
            $line = $this->doIf($line);
            $line = $this->doElseIf($line);
            $line = $this->doElse($line);
            $line = $this->doEnd($line);
            $line = $this->doForeach($line);
            $line = $this->doFor($line);
            $line = $this->doWhile($line);
            $line = $this->doDo($line);

            $phtml[] = $line;
        }

        $result = implode(PHP_EOL, $phtml);
        $result = str_replace('<? ', '<?php ', $result);

        $this->result = $result;
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

}