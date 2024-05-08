<?php
namespace Ephect\Plugins\DBAL;

class SqlParameters
{
    public $Host = '';
    public $User = '';
    public $Password = '';
    public $DatabaseName = '';
    public $ServerType = 0;

    public function __construct(string $host, string $user, string $password, string $databaseName, string $serverType)
    {
        $this->Host = $host;
        $this->User = $user;
        $this->Password = $password;
        $this->DatabaseName = $databaseName;
        $this->ServerType = $serverType;
    }

}
