<?php
namespace Ephect\Plugins\DBAL\CLient\PDO\SchemaInfo;

use mysqli;

class PdoMySQLSchemaInfo extends CustomPdoSchemaInfo
{
    public function getInfo($index): ?object
    {
        if ($this->result === null) {
            try {
                $connection = new mysqli(
                    $this->config->getHost(),
                    $this->config->getUser(),
                    $this->config->getPassword(),
                    $this->config->getDatabaseName(),
                    ($this->config->getPort() !== '') ? $this->config->getPort() : null
                );

                $this->result = $connection->query($this->sql);
            } catch (\Exception $ex) {
                return null;
            }
        }

        $this->info = $this->result->fetch_field_direct($index);

        return $this->info;
    }

    public function setTypes(): void
    {
        $this->native_types = (array) null;
        $this->native2php_assoc = (array) null;
        $this->native2php_num = (array) null;

        $this->native_types[1] = "TINYINT";
        $this->native_types[2] = "SMALLINT";
        $this->native_types[3] = "INT";
        $this->native_types[4] = "FLOAT";
        $this->native_types[5] = "DOUBLE";
        $this->native_types[7] = "TIMESTAMP";
        $this->native_types[8] = "BIGINT";
        $this->native_types[9] = "MEDIUMINT";
        $this->native_types[10] = "DATE";
        $this->native_types[11] = "TIME";
        $this->native_types[12] = "DATETIME";
        $this->native_types[13] = "YEAR";
        $this->native_types[16] = "BIT";
        $this->native_types[246] = "DECIMAL";
        $this->native_types[252] = "BLOB";
        $this->native_types[253] = "VARCHAR";
        $this->native_types[254] = "CHAR";

        $this->native2php_assoc["TINYINT"] = "int";
        $this->native2php_assoc["SMALLINT"] = "int";
        $this->native2php_assoc["INT"] = "int";
        $this->native2php_assoc["FLOAT"] = "float";
        $this->native2php_assoc["DOUBLE"] = "float";
        $this->native2php_assoc["TIMESTAMP"] = "int";
        $this->native2php_assoc["BIGINT"] = "int";
        $this->native2php_assoc["MEDIUMINT"] = "int";
        $this->native2php_assoc["DATE"] = "date";
        $this->native2php_assoc["TIME"] = "time";
        $this->native2php_assoc["DATETIME"] = "datetime";
        $this->native2php_assoc["YEAR"] = "year";
        $this->native2php_assoc["BIT"] = "int";
        $this->native2php_assoc["DECIMAL"] = "float";
        $this->native2php_assoc["BLOB"] = "blob";
        $this->native2php_assoc["VARCHAR"] = "string";
        $this->native2php_assoc["CHAR"] = "char";

        $this->native2php_num[1] = "int";
        $this->native2php_num[2] = "int";
        $this->native2php_num[3] = "int";
        $this->native2php_num[4] = "float";
        $this->native2php_num[5] = "float";
        $this->native2php_num[7] = "int";
        $this->native2php_num[8] = "int";
        $this->native2php_num[9] = "int";
        $this->native2php_num[10] = "date";
        $this->native2php_num[11] = "time";
        $this->native2php_num[12] = "datetime";
        $this->native2php_num[13] = "year";
        $this->native2php_num[16] = "int";
        $this->native2php_num[246] = "float";
        $this->native2php_num[252] = "blob";
        $this->native2php_num[253] = "string";
        $this->native2php_num[254] = "string";
    }

    public function getShowTablesQuery(): string
    {
        $sql = <<<SQL
        show tables from {$this->config->getDatabaseName()};
        SQL;

        return $sql;
    }

    public function getShowFieldsQuery(?string $table): string
    {
        $sql = <<<SQL
        show fields from $table;
        SQL;

        return $sql;
    }
    
    public function getFieldCount() : int
    {}
}
