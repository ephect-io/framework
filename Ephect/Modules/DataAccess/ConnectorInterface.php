<?php

namespace Ephect\Modules\DataAccess;

interface ConnectorInterface
{
    function connect();

    function query($sql);

    function queryLog($sql, $filename, $lineNumber);

    function formatLimitedQyery($sql, $offset, $count);

    function fetchArray($resource);

    function nextResult($resource);

    function fetchObject($resource);

    function numRows($resource);

    function numFields($resource);

    function fieldName($resource, $offset);

    function fieldLen($resource, $offset);

    function fieldType($resource, $offset);

    function close();

    function freeResult($resource);

    function error();

    function useTransactions($set);

    function beginTransaction();

    function getTransactionLevel();

    function commit();

    function rollback();

    function identity();

    function getRecordset($sql);

}
