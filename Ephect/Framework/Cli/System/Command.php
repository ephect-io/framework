<?php

namespace Ephect\Framework\CLI\System;

use Ephect\Framework\CLI\Console;

class Command
{
    public function execute(string $cmd, ...$args): void
    {
        \pcntl_exec($cmd, $args);

    }

    public function which($bin): ?string
    {
        $result = null;

        $cleanBin = preg_replace('/([\w]+)/', '$1', $bin);

        if ($cleanBin !== $bin) {
            return $result;
        }

        $result = exec("which $bin", $output, $code);

        return $result;
    }
}