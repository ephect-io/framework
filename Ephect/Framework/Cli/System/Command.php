<?php

namespace Ephect\Framework\CLI\System;

use function pcntl_exec;

class Command
{
    public function execute(string $cmd, ...$args): void
    {
        pcntl_exec($cmd, $args);

    }

    public function which($bin): ?string
    {
        $result = null;

        $cleanBin = preg_replace('/([\w]+)/', '$1', $bin);

        if ($cleanBin !== $bin) {
            return null;
        }

        $result = exec("which $bin", $output, $code);

        return $result;
    }
}