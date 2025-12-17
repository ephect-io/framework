<?php

namespace Ephect\Modules\DataAccess\Client\PDO\SchemaInfo;

use Ephect\Framework\Logger\Logger;
use PDOException;
use SQLite3;

class PdoSqliteSchemaInfo extends AbstractPdoSchemaInfo
{
    public function getInfo($index): ?object
    {
        $name = '';
        $type = '';
        $len = 0;

        $connection = new SQLite3(
            $this->config->getDatabaseName()
        );

        if ($this->columnNames === null && $this->isQueryATable()) {
            $this->columnNames = [];
            $this->columnTypes = [];
            $table = $this->getQuery();
            $sql = <<<SQL
                SELECT * FROM PRAGMA_TABLE_INFO('{$table}');
                SQL;

            $this->result = $connection->query($sql);

            $names = [];
            $types = [];
            while ($row = $this->result->fetchArray()) {
                $this->columnNames[] = $row[1];
                $this->columnTypes[] = $row[2];
            }
        }

        if ($this->columnTypes !== null) {
            $name = $this->columnNames[$index];
            $type = $this->columnTypes[$index];
            $len = 1024;
        }

        if ($name == '' && !$this->isQueryATable()) {
            try {
                $sql = $this->getQuery();

                $this->result = $connection->query($sql);
                // $columns = $this->result->fetchArray();
                // $columnsCount = count($columns);

                // $sql = str_replace("\n", " ", $this->sql);
                // $sql = str_replace("\r", " ", $sql);
                // $sql = strtolower($sql);
                // $from = \strpos($sql, 'from');
                // $query = \substr($sql, $from - 1);

                // $columnsTypeofArray = array_map(function($columnName) {
                //     return "typeof(" . $columnName . ")";
                // }, $columns );

                // $columnsTypeof = join(', ', $columnsTypeofArray);
                // $sql = 'select ' . $columnsTypeof . $query;
                // self::getLogger()->sql($sql);
                // $this->result = $connection->query($query);
                // $columnsTypes = $this->result->fetchArray();
                // $type = $columnsTypes[$index];

                $name = $this->result->columnName($index);
                $type = $this->result->columnType($index);
                $len = 32768;
            } catch (PDOException $ex) {
                Logger::create()->error($ex);
            }
        }

        $this->info = (object)['name' => $name, 'type' => $type, 'length' => $len];

        return $this->info;
    }

    public function setTypes(): void
    {
        $this->native_types = (array)null;
        $this->native2php_assoc = (array)null;
        $this->native2php_num = (array)null;

        $this->native_types[1] = "INTEGER";
        $this->native_types[2] = "TEXT";
        $this->native_types[3] = "BLOB";
        $this->native_types[4] = "REAL";
        $this->native_types[5] = "NUMERIC";

        $this->native2php_assoc["INTEGER"] = "int";
        $this->native2php_assoc["TEXT"] = "string";
        $this->native2php_assoc["BLOB"] = "blob";
        $this->native2php_assoc["REAL"] = "float";
        $this->native2php_assoc["NUMERIC"] = "float";

        $this->native2php_num[1] = "int";
        $this->native2php_num[2] = "string";
        $this->native2php_num[3] = "blob";
        $this->native2php_num[4] = "float";
        $this->native2php_num[5] = "float";
    }

    public function getShowTablesQuery(): string
    {
        $sql = <<<SQL
            SELECT
                name
            FROM
                sqlite_master
            WHERE
                type ='table' AND
                name NOT LIKE 'sqlite_%';
            SQL;

        return $sql;
    }

    public function getFieldCount(): int
    {
        $result = 0;

        $connection = new SQLite3(
            $this->config->getDatabaseName()
        );

        if ($this->isQueryATable()) {
            $sql = $this->getShowFieldsQuery($this->getQuery());
            $stmt = $connection->query($sql);
            $count = 0;
            while ($row = $stmt->fetchArray()) {
                $count++;
            }
            $result = $count;
        }

        return $result;
    }

    public function getShowFieldsQuery(?string $table): string
    {
        $sql = <<<SQL
            SELECT name FROM PRAGMA_TABLE_INFO('{$table}');
            SQL;

        return $sql;
    }

    public function getRowCount(): int
    {
        $result = 0;

        $connection = new SQLite3(
            $this->config->getDatabaseName()
        );

        $sql = $this->getQuery();
        $stmt = $connection->query($sql);
        $count = 0;
        while ($row = $stmt->fetchArray()) {
            $count++;
        }
        $result = $count;

        return $result;
    }
}
