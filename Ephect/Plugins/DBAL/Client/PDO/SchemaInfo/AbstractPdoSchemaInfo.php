<?php

namespace Ephect\Plugins\DBAL\CLient\PDO\SchemaInfo;

use Ephect\Framework\Configuration\ConfigurableInterface;
use Ephect\Framework\Logger\Logger;
use Ephect\Plugins\DBAL\Client\PDO\PdoConfiguration;
use Ephect\Plugins\DBAL\DataStatementInterface;
use Ephect\Plugins\DBAL\ServerType;
use PDOException;

abstract class AbstractPdoSchemaInfo implements PdoSchemaInfoInterface
{
    protected ConfigurableInterface|null $config = null;
    protected string $driver = ServerType::SQLITE;
    protected DataStatementInterface $statement;
    protected array $values;
    protected int $fieldCount;
    protected int $rowCount;
    protected array $meta = [];
    protected array|null $columnNames = null;
    protected array|null $columnTypes = null;
    protected string $query = '';
    protected mixed $result;
    protected array $native_types = [];
    protected array $native2php_assoc = [];
    protected array $native2php_num = [];
    protected object|null $typesMapper = null;
    protected string|null $cs = null;
    protected object|null $info = null;
    protected bool $queryIsATable = false;

    public function __construct(PdoConfiguration $config)
    {
        $this->config = $config;
        $this->setTypes();
    }

    public function setTypes(): void
    {
    }

    public static function builder(PdoConfiguration $config): AbstractPdoSchemaInfo
    {
        $result = null;

        try {
            if ($config->getDriver() == ServerType::MYSQL) {
                $result = new PdoMySQLSchemaInfo($config);
            }

            if ($config->getDriver() == ServerType::SQLITE) {
                $result = new PdoSQLiteSchemaInfo($config);
            }
        } catch (PDOException $ex) {
            Logger::create()->error($ex);
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $value): void
    {
        $this->query = $value;
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
