<?php

namespace Ephect\Modules\Authentication\Commands\UserMigration;

use Ephect\Framework\Commands\AbstractCommandLib;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\Enums\ConsoleOptionsEnum;
use Ephect\Modules\DoctrineBridge\DBAL\Connection;
use Ephect\Modules\DoctrineBridge\ORM\MetadataConfig;
use Exception;
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

            $doUp = $this->application->hasCommandLineOption('u', 'up');
            $doDown = $this->application->hasCommandLineOption('d', 'down');
            $doError = $this->application->hasCommandLineOption('u', 'up') && $this->application->hasCommandLineOption('d', 'down');

            $doNothing = !$doUp && !$doDown;

            if ($doNothing) {
                throw new InvalidArgumentException('Missing arguments.');
            }

            if ($doError) {
                throw new InvalidArgumentException('Invalid arguments.');
            }

            $version = null;
            if ($doUp) {
                $version = $this->application->getCommandLineOption('u', 'up');
            } else if ($doDown) {
                $version = $this->application->getCommandLineOption('d', 'down');
            }   else {
                throw new InvalidArgumentException('Invalid arguments.');
            }

            Console::writeLine("Running migration for version: $version");

            try {

                $this->connection->beginTransaction();

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
                throw new Exception("Error executing SQL: " . $exception->getMessage(), previous: $exception);
            } catch (Throwable $throwable) {
                $this->connection->rollBack();
                throw $throwable;
            }   

        } catch (Exception|Throwable $exception) {
            Console::error($exception, ConsoleOptionsEnum::ErrorMessageOnly);
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
        $table->addColumn('first_name', Types::STRING, ['length' => 255]);
        $table->addColumn('last_name', Types::STRING, ['length' => 255]);
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

        Console::writeLine('Users table has been created.');
    }

    /**
     * @throws Exception
     */
    private function doDown(AbstractSchemaManager $schema): void
    {
        if (!$schema->tableExists('users')) {
            Console::writeLine('Nothing to drop.');
            return;
        }
        $schema->dropTable('users');

        Console::writeLine('Users table has been dropped.');
    }

}
