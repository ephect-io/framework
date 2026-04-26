<?php

namespace Ephect\Modules\DoctrineBridge\Hooks;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;

function useEntityManager(ConnectionConfiguration $connectionConfig, bool $isDevMode = false): EntityManager
{
    $settings = $connectionConfig->getStructure()->encode(asArray: true);
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

    $connection = DriverManager::getConnection($settings, $ormConfig);

    return new EntityManager($connection, $ormConfig);
}
