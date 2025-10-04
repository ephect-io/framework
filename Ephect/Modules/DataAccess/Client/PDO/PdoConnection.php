<?php

namespace Ephect\Modules\DataAccess\Client\PDO;

use Ephect\Framework\Configuration\AbstractConfiguration;
use Ephect\Modules\DataAccess\Client\Base\SqlConnectionInterface;
use Ephect\Modules\DataAccess\Client\Base\Type\ServerType;
use Ephect\Modules\DataAccess\Client\PDO\Exceptions\PdoConnectionException;
use Ephect\Modules\DataAccess\Client\PDO\SchemaInfo\AbstractPdoSchemaInfo;
use Ephect\Modules\DataAccess\CRUD\CrudQueriesTrait;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

class PdoConnection extends AbstractConfiguration implements SqlConnectionInterface
{
    use CrudQueriesTrait;
    private PDO $_state;
    private PdoConfiguration $_config;
    private string $_dsn = '';
    private array|null $_params = null;
    private AbstractPdoSchemaInfo $_SchemaInfo;

    public function __construct(PdoConfiguration $config)
    {
        parent::__construct($this);

        $this->_config = $config;
        $this->configure();
    }

    public function configure(): void
    {
        $this->_dsn = '';
        $this->_params = (array)null;
        if ($this->_config->getDriver() == ServerType::MYSQL) {
            $this->_params = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
            $this->_dsn = $this->_config->getDriver() . ':host=' . $this->_config->getHost() . ';dbname=' . $this->_config->getDatabaseName();
        } elseif ($this->_config->getDriver() == ServerType::SQLSERVER) {
            $this->_params = [PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_SYSTEM, PDO::SQLSRV_ATTR_DIRECT_QUERY => true];
            $this->_dsn = $this->_config->getDriver() . ':Server=' . $this->_config->getHost() . ';Database=' . $this->_config->getDatabaseName();
        } elseif ($this->_config->getDriver() == ServerType::SQLITE) {
            $this->_dsn = $this->_config->getDriver() . ':' . $this->_config->getDatabaseName();
        }
        // $this->_SchemaInfo = AbstractPdoSchemaInfo::builder($this->_config);
    }

    public function getDriver(): string
    {
        return $this->_config->getDriver();
    }

    public static function opener($confname): PdoConnection
    {
        $result = self::builder($confname);
        $result->open();

        return $result;
    }

    public static function builder(string $confname): PdoConnection
    {
        $result = null;
        try {
            $config = new PdoConfiguration();
            if (!$config->loadConfiguration($confname)) {
                throw new Exception("The configuration `$confname` was not found.");
            }
            $result = new PdoConnection($config);
        } catch (Exception | PDOException $e) {
            throw new PdoConnectionException('Something went wrong while building the connection.', 1, $e);
        } finally {
            return $result;
        }
    }

    public function open(): PDO
    {
        try {
            if ($this->_params != null) {
                $this->_state = new PDO($this->_dsn, $this->_config->getUser(), $this->_config->getPassword(), $this->_params);
            } else {
                $this->_state = new PDO($this->_dsn, $this->_config->getUser(), $this->_config->getPassword(), []);
            }
        } catch (PDOException $ex) {
            self::getlogger()->error($ex, __FILE__, __LINE__);
        }

        return $this->_state;
    }

    public function getSchemaInfo(): AbstractPdoSchemaInfo
    {
        return $this->_SchemaInfo;
    }

    public function getState(): PDO
    {
        return $this->_state;
    }

    public function getConfiguration(): PdoConfiguration
    {
        return $this->_config;
    }

    public function querySelect(): ?PdoDataStatement
    {
        list($sql, $params) = $this->getSelectQuery();
        return $this->query($sql, $params);
    }

    public function query(string $sql = '', ?array $params = null): ?PdoDataStatement
    {
        $statement = null;
        $result = null;
        $error = null;
        try {
            if ($params != null) {
                $statement = $this->_state->prepare($sql);
                $statement->execute($params);
            } else {
                $statement = $this->_state->query($sql);
            }

            if (!$statement && count($this->_state->errorInfo()) > 0) {
                throw new PDOException($this->_state->errorInfo()[2], $this->_state->errorInfo()[1], null);
            }
        } catch (Exception | PDOException $ex) {
            self::getLogger()->sql($sql);
            if (is_array($params)) {
                self::getLogger()->sql('WITH PARAMS ' . print_r($params, true));
            }
            self::getLogger()->error($ex);
            $error = $ex;
            $statement = null;
        } finally {
            $result = new PdoDataStatement($statement, $this, $sql, $error);
        }

        return $result;
    }

    public function prepare(string $sql): ?PDOStatement
    {
        return $this->_state->prepare($sql);
    }

    public function queryInsert(): int
    {
        return $this->exec($this->getInsertQuery());
    }

    public function exec(string $sql): string
    {
        return $this->_state->exec($sql);
    }

    public function queryUpdate(): int
    {
        return $this->exec($this->getUpdateQuery());
    }

    public function queryDelete(): int
    {
        return $this->exec($this->getDeleteQuery());
    }

    public function addSelectLimit($start, $count)
    {
        //        $driver = strtolower($this->_activeConnection->getDriver());
        //        if(strstr($driver, 'mysql')) {
        $start = (!$start) ? 1 : $start;
        $sql = $this->getSelectQuery() . PHP_EOL . ' LIMIT ' . (($start - 1) * $count) . ', ' . $count . PHP_EOL;

        $this->setSelectQuery($sql);
        //        }
    }

    public function showTables(): ?PdoDataStatement
    {
        $sql = $this->_SchemaInfo->getShowTablesQuery();
        return $this->query($sql);
    }

    public function showFieldsFrom(string $table): ?PdoDataStatement
    {
        $sql = $this->_SchemaInfo->getShowFieldsQuery($table);
        return $this->query($sql);
    }

    public function beginTransaction(): void
    {
        $this->_state->beginTransaction();
    }

    public function commit(): void
    {
        $this->_state->commit();
    }

    public function rollback(): void
    {
        $this->_state->rollBack();
    }

    public function inTransaction(): void
    {
        $this->_state->inTransaction();
    }

    public function setAttribute(string $key, $value): void
    {
        $this->_state->setAttribute($key, $value);
    }

    public function getAttribute(string $key)
    {
        return $this->_state->getAttribute($key);
    }

    public function getLastInsertId(): int
    {
        return $this->_state->lastInsertId();
    }

    public function lastInsertId(): int
    {
        return $this->_state->lastInsertId();
    }

    public function quote(string $value): string
    {
        return $this->_state->quote($value);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): bool
    {
        unset($this->_state);
        return true;
    }
}
