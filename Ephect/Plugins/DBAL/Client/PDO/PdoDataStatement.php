<?php
namespace Ephect\Plugins\DBAL\Client\PDO;

use Ephect\Framework\Element;
use Ephect\Plugins\DBAL\DataStatementInterface;

use PDOStatement;
use PDOException;

class PdoDataStatement extends Element implements DataStatementInterface
{
    private $_statement;
    private $_values;
    private $_fieldCount;
    private $_rowCount;
    private $_meta = array();
    private $_colNames = [];
    private $_schemaInfo = null;
    private $_config = null;
    private $_connection = null;
    private $_native_connection = null;
    private $_sql = '';
    private $_driver;
    private $_exception = null;
    private $_hasException = false;

    public function __construct(?PDOStatement $statement, ?PdoConnection $connection = null, ?string $sql = null, ?PDOException $error = null)
    {
        $this->_statement = $statement;
        $this->_sql = $sql;
        $this->_exception = $error;
        $this->_hasException = ($error instanceof \PDOException);

        if ($connection !== null) {
            $this->_connection = $connection;
            $this->_native_connection = $connection->getState();
            $this->_config = $connection->getConfiguration();
            $this->_driver = $this->_config->getDriver();
            $this->_schemaInfo = $connection->getSchemaInfo();
            if ($sql !== null) {
                $this->_schemaInfo->setQuery($sql);
            }
        }
    }

    public function hasException(): bool
    {
        return $this->_hasException;
    }

    public function getException(): ?\PDOException
    {
        return $this->_exception;
    }

    public function getValue($i): array
    {
        return $this->_values[$i];
    }

    public function fetch(int $mode = \PDO::FETCH_NUM): ?array
    {
        $this->_values = $this->_statement->fetch($mode);
        return (!$this->_values) ? null : $this->_values;
    }

    public function fetchAll(int $mode = \PDO::FETCH_NUM): ?array
    {
        $this->_values = $this->_statement->fetchAll($mode);
        return (!$this->_values) ? null : $this->_values;
    }

    public function fetchAssoc(): ?array
    {
        $this->_values = $this->_statement->fetch(\PDO::FETCH_ASSOC);
        return (!$this->_values) ? null : $this->_values;
    }

    public function fetchAllAssoc(): ?array
    {
        $this->_values = $this->_statement->fetchAll(\PDO::FETCH_ASSOC);
        return (!$this->_values) ? null : $this->_values;
    }

    public function fetchObject(): ?object
    {
        return $this->_statement->fetchObject();
    }

    public function getFieldCount(): ?int
    {
        if (!isset($this->_fieldCount)) {
            try {
                $this->_fieldCount = $this->_statement->columnCount();
            } catch (\Exception | \PDOException $ex) {
                if (isset($this->_values[0])) {
                    $this->_fieldCount = count($this->_values[0]);
                } else {
                    throw new \Exception("Cannot count fields of a row before the resource is fetched", -1, $ex);
                }
            }
        }

        return $this->_fieldCount;
    }

    public function getRowCount(): ?int
    {
        if (!isset($this->_rowCount)) {
            if ($this->_schemaInfo !== null) {
                $this->_rowCount = $this->_schemaInfo->getRowCount();
            } else {

                try {
                    $this->_rowCount = $this->_statement->rowCount();
                } catch (\Exception | \PDOException $ex) {
                    if (is_array($this->_values)) {
                        $this->_rowCount = count($this->_values);
                    } else {
                        throw new \Exception("Cannot count rows of a result set before the resource is fetched", -1, $ex);
                    }
                }
            }
        }

        return $this->_rowCount;
    }

    public function getFieldNames(): array
    {
        if (count($this->_colNames) == 0 && $this->_connection !== null) {

            $c = $this->getFieldCount();
            for ($i = 0; $i < $c; $i++) {
                $this->_colNames[] = $this->getFieldName($i);
            }
        }

        return $this->_colNames;
    }

    public function getFieldName($i): string
    {
        $name = '';

        if ($this->_schemaInfo !== null) {
            $info = $this->_schemaInfo->getInfo($i);
            $name = $info->name;
        } else {
            if (!isset($this->_meta[$i])) {
                $this->_meta[$i] = $this->_statement->getColumnMeta($i);
            }
            $name = $this->_meta[$i]['name'];
        }

        return $name;
    }

    public function getFieldType(int $i): string
    {
        $type = '';

        if ($this->_schemaInfo !== null) {
            $info = $this->_schemaInfo->getInfo($i);
            $type = $info->type;
        } else {
            if (!isset($this->_meta[$i])) {
                $this->_meta[$i] = $this->_statement->getColumnMeta($i);
            }
            $type = $this->_meta[$i]['native_type'];
        }

        return $type;
    }

    public function getFieldLen(int $i): int
    {
        $len = 0;

        if ($this->_schemaInfo !== null) {
            $info = $this->_schemaInfo->getInfo($i);
            $len = $info->length;
        } else {
            if (!isset($this->_meta[$i])) {
                $this->_meta[$i] = $this->_statement->getColumnMeta($i);
            }
            $len = $this->_meta[$i]['len'];
        }

        return $len;
    }

    public function typeNumToName(int $type): string
    {
        return $this->_schemaInfo->typeNumToName($type);
    }

    public function typeNameToPhp(string $type): string
    {
        return $this->_schemaInfo->typeNameToPhp($type);
    }

    public function typeNumToPhp(int $type): string
    {
        return $this->_schemaInfo->typeNumToPhp($type);
    }
}
