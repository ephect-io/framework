<?php

namespace Ephect\Modules\DataAccess\Client\Base;

interface SqlConnectionInterface extends ConnectionInterface
{
    public function query(string $sql, array $params = []);

    public function beginTransaction();

    public function commit();

    public function rollback();
}
