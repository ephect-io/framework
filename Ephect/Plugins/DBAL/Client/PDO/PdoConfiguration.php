<?php
namespace Ephect\Plugins\DBAL\Client\PDO;

class PdoConfiguration extends JsonConfiguration
{
    private $_driver = '';
    private $_host = '';
    private $_databaseName = '';
    private $_user = '';
    private $_password = '';
    private $_port = '';

    public function __construct(?string $driver = '', ?string $databaseName = '', ?string $host = '', ?string $user = '', ?string $password = '', ?string $port = '')
    {
        $this->_driver = $driver;
        $this->_databaseName = $databaseName;
        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_port = $port;

        $this->canConfigure = false;
    }

    public function loadConfiguration(string $confname): bool
    {
        $result = false;

        if (file_exists($confname)) {
            $result = parent::loadConfiguration($confname);

            return $result;
        }

        if (TRegistry::exists('connections', $confname)) {
            $this->canConfigure = false;
            $this->contents = TRegistry::read('connections', $confname);

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
        if($this->_driver == TServerType::SQLITE) {
            $this->_databaseName = APP_DATA . $this->_databaseName;
        }
        $this->_host = isset($this->contents['host']) ? $this->contents['host'] : '';
        $this->_user = isset($this->contents['user']) ? $this->contents['user'] : '';
        $this->_password = isset($this->contents['password']) ? $this->contents['password'] : '';
        $this->_port = isset($this->contents['port']) ? $this->contents['port'] : '';
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
