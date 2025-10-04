<?php

namespace Ephect\Framework\CLI\System;

use Ephect\Framework\CLI\Console;
use Exception;

class Command
{
    public function execute(string $cmd, ...$args): int
    {
        $fqcmd = $cmd . ' ' . implode(' ', $args);
        $return = system($fqcmd, $returnCode);

        if (false === $return) {
            throw new Exception('Something went wrong while trying to execute: ' . $fqcmd . '.');
        }
        Console::writeLine($return);
        return $returnCode;
    }

    public function which($bin): ?string
    {
        $result = null;

        $cleanBin = preg_replace('/([\w]+)/', '$1', $bin);

        if ($cleanBin !== $bin) {
            return null;
        }

        $cmd = "which $bin";
        if (PHP_OS == 'WINNT') {
            $cmd = "cmd /c where $bin";
        }

        return exec($cmd, $output, $code);
    }
}
