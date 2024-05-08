<?php
namespace Ephect\Plugins\DBAL\CLient\PDO\SchemaInfo;

use Ephect\Framework\Logger\Logger;
use Ephect\Plugins\DBAL\Client\PDO\PdoConfiguration;
use Ephect\Plugins\DBAL\TServerType;

use PDOException;

abstract class CustomPdoSchemaInfo implements PdoSchemaInfoInterface
{
    protected $config = null;
    protected $driver = '';
    protected $statement;
    protected $values;
    protected $fieldCount;
    protected $rowCount;
    protected $meta = [];
    protected $columnNames = null;
    protected $columnTypes = null;
    protected $query = '';
    protected $result = null;
    protected $native_types = [];
    protected $native2php_assoc = [];
    protected $native2php_num = [];
    protected $typesMapper = null;
    protected $cs = null;
    protected $info = null;
    protected $queryIsATable = false;

    public function __construct(PdoConfiguration $config)
    {
        $this->config = $config;
        $this->setTypes();
    }

    public static function builder(PdoConfiguration $config): CustomPdoSchemaInfo
    {
        $result = null;

        try {
            if ($config->getDriver() == TServerType::MYSQL) {
                $result = new PdoMySQLSchemaInfo($config);
            }

            if ($config->getDriver() == TServerType::SQLITE) {
                $result = new PdoSQLiteSchemaInfo($config);
            }
        } catch (PDOException $ex) {
            Logger::create()->error($ex);
            $result = null;
        } finally {
            return $result;
        }
    }

    public function setQuery(string $value): void
    {
        $this->query = $value;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getColumnNames(): ?array
    {
        return $this->columnNames;
    }

    public function getColumnTypes(): ?array
    {
        return $this->columnTypes;
    }

    public function isQueryATable(): bool
    {
        if (!$this->queryIsATable) {
            $sql = str_replace("\r", ' ', $this->query);
            $sql = str_replace("\n", ' ', $sql);
            $sql = trim($sql);
            $this->queryIsATable = (strpos($sql, ' ') === false);
        }
        return $this->queryIsATable;
    }

    public function setTypes(): void
    {
    }

    public function getInfo(int $index): ?object
    {
    }

    public function typeNumToName(int $type): string
    {
        return $this->native_types[$type];
    }

    public function typeNameToPhp(string $type): string
    {
        return $this->native2php_assoc[$type];
    }

    public function typeNumToPhp(int $type): string
    {
        return $this->native2php_num[$type];
    }

    public function getShowTablesQuery(): string
    {
    }

    public function getShowFieldsQuery(?string $table): string
    {
    }

    public function getFieldCount(): int
    {
    }
    
    public function getRowCount(): int
    {
    }
}
