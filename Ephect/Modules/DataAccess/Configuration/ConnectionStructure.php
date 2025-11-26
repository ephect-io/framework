<?php

namespace Ephect\Modules\DataAccess\Configuration;

use Ephect\Framework\Structure\JsonProperty;
use Ephect\Framework\Structure\Structure;

class ConnectionStructure extends Structure
{
    public string $driver;
    public string $user;
    public string $password;
    #[JsonProperty(name: 'dbname')]
    public string $databaseName;
    public string $host;
    public int $port;
    public string $charset = 'utf8mb4';
}
