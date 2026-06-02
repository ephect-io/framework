<?php

namespace Ephect\Modules\Authentication\Commands\UserMigration;

use Ephect\Framework\Commands\AbstractCommandLib;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Ephect\Framework\CLI\Application;
use Ephect\Modules\DoctrineBridge\DBAL\Connection;
use Ephect\Modules\DoctrineBridge\ORM\MetadataConfig;
use InvalidArgumentException;
use Throwable;

use function Ephect\Modules\DataAccess\Hooks\useConfig;

class Lib extends AbstractCommandLib
{

    private DBALConnection $connection;

    public function __construct(private Application $application)
    {
        $connectionConfig = useConfig('devrez-postgres');
        $config = MetadataConfig::create();

        $this->connection = Connection::create($connectionConfig, $config);
    }

    public function execute(): void
    {
        try
        {
            $params = $this->application->getArgv();

            $doUp = array_key_exists('u', $params) || array_key_exists('up', $params);
            $doDown = array_key_exists('d', $params) || array_key_exists('down', $params);
            $doError = array_key_exists('u', $params) && array_key_exists('up', $params);
            $doError = $doError || (array_key_exists('d', $params) && array_key_exists('down', $params));
            $doError = $doError || ($doUp && $doDown);
            $doNothing = !$doUp && !$doDown;

            if ($doNothing) {
                throw new InvalidArgumentException('Missing arguments.');
            }

            if ($doError) {
                throw new InvalidArgumentException('Invalid arguments.');
            }

            $this->connection->beginTransaction();

            if ($doUp) {
                $version = !isset($params['u']) ? ($params['up'] ?? null) : $params['u'];
            } else if ($doDown) {
                $version = !isset($params['d']) ? ($params['down'] ?? null) : $params['d'];
            }

            $schemaMan = $this->connection->createSchemaManager();
            $schema = new Schema();

            if ($doUp) {
                $this->doUp($schemaMan);
            } else if ($doDown) {
                $this->doDown($schemaMan);
            }
            // Execute the SQL query
            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            foreach ($sqlArray as $sql) {
                $this->connection->executeQuery($sql);
            }

            $this->connection->commit();

        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw $exception;
        } catch (Throwable $throwable) {
            $this->connection->rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws Exception
     */
    private function doUp(AbstractSchemaManager $schema): void
    {
        if ($schema->tableExists('users')) {
            return;
        }

        $table = new Table('users');
        $table->addColumn('id', Types::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('email', Types::STRING, ['length' => 255]);
        $table->addColumn('password', Types::STRING, ['length' => 60]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setUnquotedName('pk_users')
                ->setUnquotedColumnNames('id')
                ->create()
        );

        $schema->createTable($table);

        echo 'Users table has been created.' . PHP_EOL;
    }

    /**
     * @throws Exception
     */
    private function doDown(AbstractSchemaManager $schema): void
    {
        if (!$schema->tableExists('users')) {
            echo 'Nothing to drop.' . PHP_EOL;
            return;
        }
        $schema->dropTable('users');

        echo 'Users table has been dropped.' . PHP_EOL;
    }

}
