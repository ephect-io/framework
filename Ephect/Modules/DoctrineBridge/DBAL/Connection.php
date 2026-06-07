<?php

namespace Ephect\Modules\DoctrineBridge\DBAL;

use Doctrine\ORM\Configuration;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;

class Connection
{
    public static function create(ConnectionConfiguration $connectionConfig, Configuration $ormConfig): DBALConnection
    {
        $settings = $connectionConfig->getStructure()->encode(asArray: true);

        return DriverManager::getConnection($settings, $ormConfig);
    }
}
