<?php

namespace Ephect\Framework\Registry;

class PharRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new PharRegistry;
        self::$instance->_setCacheDirectory(RUNTIME_DIR);
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new PharRegistry;
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        FrameworkRegistry::load(true);
        $items = FrameworkRegistry::items();

        foreach ($items as $key => $value) {

            $value = str_replace(\Constants::EPHECT_ROOT, '', $value);
            $value = str_replace(DIRECTORY_SEPARATOR, '_', $value);

            PharRegistry::write($key, $value);
        }

        PharRegistry::save();
    }
}
