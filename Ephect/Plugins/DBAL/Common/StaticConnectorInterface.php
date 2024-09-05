<?php
namespace Ephect\Plugins\DBAL;

interface StaticConnectorInterface {
    static function connect();
    static function isAlive();
    static function isConnected();
    static function query($sql);
    static function queryLog($sql, $filename, $lineNumber);
    static function formatLimitedQyery($sql, $offset, $count);
    static function fetchArray($resource);
    static function nextResult($resource);
    static function fetchObject($resource);
    static function numRows($resource);
    static function numFields($resource);
    static function fieldName($resource, $offset);
    static function fieldLen($resource, $offset);
    static function fieldType($resource, $offset);
    static function close();
    static function kill();
    static function freeResult($resource);
    static function error();
    static function useTransactions($set);
    static function beginTransaction();
    static function getTransactionLevel();
    static function commit();
    static function rollback();
    static function identity();
    static function getRecordset($sql);
}
