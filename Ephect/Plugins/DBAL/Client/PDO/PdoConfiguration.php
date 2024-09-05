<?php
namespace Ephect\Plugins\DBAL\Client\PDO;

use Ephect\Framework\Registry\StateRegistry;
use Ephect\Plugins\DBAL\Data\JsonConfiguration;
use Ephect\Plugins\DBAL\ServerType;

class PdoConfiguration extends JsonConfiguration
{

    public function __construct(
        private string $_driver = '',
        private string $_host = '',
        private string $_databaseName = '',
        private string $_user = '',
        private string $_password = '',
        private string $_port = '',
    )
    {
        parent::__construct($this);
        $this->canConfigure = false;
    }

    public function loadConfiguration(string $filename): bool
    {
        $result = false;

        if (file_exists($filename)) {
            $result = parent::loadConfiguration($filename);

            return $result;
        }

        if (StateRegistry::exists('connections', $filename)) {
            $this->canConfigure = false;
            $this->contents = StateRegistry::readItem('connections', $filename);

            $this->configure();

            $result = true;
        }

        return $result;
    }

    public function configure(): void
    {
        if ($this->canConfigure) {
            parent::configure();
        }

        $this->_driver = $this->contents['driver'];
        $this->_databaseName = $this->contents['database'];
        if($this->_driver == ServerType::SQLITE) {
            $this->_databaseName = APP_DATA . $this->_databaseName;
        }
        $this->_host = !isset($this->contents['host']) ? '' : $this->contents['host'];
        $this->_user = !isset($this->contents['user']) ? '' : $this->contents['user'];
        $this->_password = !isset($this->contents['password']) ? '' : $this->contents['password'];
        $this->_port = !isset($this->contents['port']) ? '' : $this->contents['port'];
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

    public function getPort(): string
    {
        return $this->_port;
    }
}
