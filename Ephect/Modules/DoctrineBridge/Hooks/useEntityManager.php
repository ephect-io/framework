<?php

namespace Ephect\Modules\DoctrineBridge\Hooks;

use Doctrine\ORM\EntityManager;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;
use Ephect\Modules\DoctrineBridge\DBAL\Connection;
use Ephect\Modules\DoctrineBridge\ORM\MetadataConfig;

function useEntityManager(ConnectionConfiguration $connectionConfig, bool $isDevMode = false): EntityManager
{
    $ormConfig = MetadataConfig::create($isDevMode);
    $connection = Connection::create($connectionConfig, $ormConfig);

    return new EntityManager($connection, $ormConfig);
}
