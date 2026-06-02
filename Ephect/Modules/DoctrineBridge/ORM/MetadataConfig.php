<?php

namespace Ephect\Modules\DoctrineBridge\ORM;

use Doctrine\ORM\ORMSetup;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;
use Doctrine\ORM\Configuration;

class MetadataConfig
{
    public ConnectionConfiguration $connectionConfig;
    public bool $isDevMode;

    public static function create(bool $isDevMode = false): Configuration
    {
        $paths = [\Constants::APP_ROOT . 'Entity'];
        $proxyDir = \Constants::RUNTIME_DIR . 'doctrine_proxies';
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir, 0755, true);
        }

        $ormConfig = ORMSetup::createAttributeMetadataConfig($paths, $isDevMode);

        if (PHP_VERSION_ID >= 80400) {
            $ormConfig->enableNativeLazyObjects(true);
        } else {
            $ormConfig->setProxyDir($proxyDir);
            $ormConfig->setProxyNamespace('DoctrineProxies');
        }

        return $ormConfig;
    }
}