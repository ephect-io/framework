<?php

namespace Ephect\Framework\Registry;

class CommandRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new CommandRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CommandRegistry();
        }

        return self::$instance;
    }
}
