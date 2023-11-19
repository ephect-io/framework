<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleOptions;
use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class PhpTagsCleaner extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $phtml = $parameter;

        Console::log($this->component->getClass());

        $re = '/\?>( |\s+?)<\?php/m';
        preg_match_all($re, $phtml, $matches, PREG_SET_ORDER, 0);

        if(count($matches) == 0) {
            $this->result = $phtml;
            return;
        }

        try {
            $phtml = preg_replace($re, "$1", $phtml);
        } catch (\Exception $ex) {
           Console::error($ex, ConsoleOptions::ErrorMessageOnly);
        }

        if($phtml == '' || $phtml == null) {
            $phtml = $parameter;
        }

        $this->result = $phtml;
    }
}