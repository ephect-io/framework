<?php

namespace Ephect\Modules\DataAccess\Configuration;

use Ephect\Framework\Configuration\AbstractConfiguration;

abstract class DataConfiguration extends AbstractConfiguration
{

    public function __construct(
        private readonly string $_driver = '',
        private readonly string $_host = '',
        private readonly string $_databaseName = '',
        private readonly string $_user = '',
        private readonly string $_password = '',
        private readonly int    $_port = 0,

    )
    {
        parent::__construct($this);

    }

    public function configure(): void
    {
    }

    public function getDriver(): string
    {
        return $this->_driver;
    }

    public function getDatabaseName(): string
    {
        return $this->_databaseName;
    }

    /*
     * Following properties are default null string in constructor because they may not be used (eg: SQLite)
     */
    public function getHost(): string
    {
        return $this->_host;
    }

    public function getUser(): string
    {
        return $this->_user;
    }

    public function getPassword(): string
    {
        return $this->_password;
    }

    public function getPort(): int
    {
        return $this->_port;
    }
}
