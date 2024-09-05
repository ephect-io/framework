<?php

namespace Ephect\Plugins\DBAL;

interface SqlConnectionInterface extends ConnectionInterface
{
    public function query(string $sql, array $params = []);

    public function beginTransaction();

    public function commit();

    public function rollback();
}
