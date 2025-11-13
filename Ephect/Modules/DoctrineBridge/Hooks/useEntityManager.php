<?php

namespace Ephect\Modules\DoctrineBridge\Hooks;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;

function useEntityManager(ConnectionConfiguration $config, bool $isDevMode = false): EntityManager
{
    $settings = $config->getStructure()->encode(asArray: true);
    $paths = [\Constants::APP_ROOT . 'Entity'];
    $proxyDir = \Constants::RUNTIME_DIR . 'doctrine_proxies';
    
    // Create proxy directory if it doesn't exist
    if (!is_dir($proxyDir)) {
        mkdir($proxyDir, 0755, true);
    }

    $config = ORMSetup::createAttributeMetadataConfig($paths, $isDevMode);
    $config->setProxyDir($proxyDir);
    $config->setProxyNamespace('DoctrineProxies');
    
    $connection = DriverManager::getConnection($settings, $config); 
    
    return new EntityManager($connection, $config);
}
