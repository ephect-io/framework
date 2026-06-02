<?php

namespace Ephect\Modules\DoctrineBridge\Mappers;

class DriverMapper
{
    private static array $mapping = [
        'pdo_mysql' => 'pdo_mysql',
        'pdo_sqlite' => 'pdo_sqlite',
        'pdo_pgsql' => 'pdo_pgsql',
        'pdo_sqlsrv' => 'pdo_sqlsrv',
    ];

    public static function map(string $driver): string
    {
        return self::$mapping[$driver] ?? throw new \InvalidArgumentException("Unsupported driver: $driver");
    }
}
