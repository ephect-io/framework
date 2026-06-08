<?php

namespace Ephect\Framework\Dbal;

use Doctrine\ORM\Configuration;
use Doctrine\DBAL\Connection as DBALConnection;
use Ephect\Modules\DoctrineBridge\DBAL\Connection;
use Ephect\Modules\DoctrineBridge\ORM\MetadataConfig;

use function Ephect\Modules\DataAccess\Hooks\useConfig;

class ConnectionFactory
{
    private Configuration $config;

    public function __construct(private readonly string $name)
    {
        $this->config = MetadataConfig::create();
    }

    public function create(): DBALConnection
    {
        $connectionConfig = useConfig($this->name);
        return Connection::create($connectionConfig, $this->config);
    }
}
