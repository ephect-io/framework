<?php

namespace Ephect\Modules\DataAccess\Client\Base;

interface StaticConnectorInterface
{
    public static function connect();

    public static function isAlive();

    public static function isConnected();

    public static function query($sql);

    public static function queryLog($sql, $filename, $lineNumber);

    public static function formatLimitedQyery($sql, $offset, $count);

    public static function fetchArray($resource);

    public static function nextResult($resource);

    public static function fetchObject($resource);

    public static function numRows($resource);

    public static function numFields($resource);

    public static function fieldName($resource, $offset);

    public static function fieldLen($resource, $offset);

    public static function fieldType($resource, $offset);

    public static function close();

    public static function kill();

    public static function freeResult($resource);

    public static function error();

    public static function useTransactions($set);

    public static function beginTransaction();

    public static function getTransactionLevel();

    public static function commit();

    public static function rollback();

    public static function identity();

    public static function getRecordset($sql);
}
