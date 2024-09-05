<?php
namespace Ephect\Plugins\DBAL;

interface DataStatementInterface
{
    public function fetch(int $mode): ?array;

    public function fetchAll(int $mode): ?array;

    public function fetchObject(): ?object;

    public function getFieldCount(): ?int;

    public function getRowCount(): ?int;

    public function getFieldName(int $i): string;

    public function getFieldType(int $i): string;

    public function getFieldLen(int $i): int;
}

?>
