<?php
namespace Ephect\Plugins\DBAL;

enum ServerTypeEnum: string
{
    case MYSQL = 'mysql';
    case SQLSERVER = 'sqlsrv';
    case SQLITE = 'sqlite';
    case PGSQL = 'pgsql';
}
