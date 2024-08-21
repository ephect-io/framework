<?php

namespace Ephect\Framework\Configuration;

interface ConfigurableInterface
{
    public function configure(): void;

    public function loadConfiguration(string $filename): bool;
}
