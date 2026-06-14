<?php

namespace Ephect\Modules\DoctrineBridge\Hooks;

use Ephect\Modules\DoctrineBridge\DBAL\Connection;
use Ephect\Modules\DoctrineBridge\ORM\MetadataConfig;
use Doctrine\DBAL\Connection as DBALonnection;

use function Ephect\Modules\DataAccess\Hooks\useConfig;

function useConnection(): DBALonnection
{
    $conf = useConfig('default');
    $orm = MetadataConfig::create();
    $connection = Connection::create($conf, $orm); 

    return $connection;
}