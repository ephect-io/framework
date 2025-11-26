<?php

namespace Ephect\Modules\DataAccess\Configuration;

use Ephect\Framework\Structure\JsonProperty;
use Ephect\Framework\Structure\StructureTrait;

final class ConnectionConfiguration
{
    private string $driver = '';
    private string $user = '';
    private string $databaseName = '';
    private string $host = '';
    private string $password = '';
    private int $port = 0;
    private string $charset = '';

    use StructureTrait;

    public function __construct(ConnectionStructure $structure)
    {
        $this->bindStructure($structure);
    }

    public function configure(): void
    {
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /*
     * Following properties are default null string in constructor because they may not be used (eg: SQLite)
     */
    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }
}
