<?php

namespace Ephect\Modules\DataAccess\Client\Base;

interface ConnectorInterface
{
    public function connect();

    public function query($sql);

    public function queryLog($sql, $filename, $lineNumber);

    public function formatLimitedQyery($sql, $offset, $count);

    public function fetchArray($resource);

    public function nextResult($resource);

    public function fetchObject($resource);

    public function numRows($resource);

    public function numFields($resource);

    public function fieldName($resource, $offset);

    public function fieldLen($resource, $offset);

    public function fieldType($resource, $offset);

    public function close();

    public function freeResult($resource);

    public function error();

    public function useTransactions($set);

    public function beginTransaction();

    public function getTransactionLevel();

    public function commit();

    public function rollback();

    public function identity();

    public function getRecordset($sql);
}
