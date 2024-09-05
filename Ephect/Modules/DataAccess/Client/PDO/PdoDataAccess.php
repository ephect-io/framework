<?php

namespace Ephect\Modules\DataAccess\Client\PDO;

use Ephect\Modules\DataAccess\Client\Base\Type\ServerType;

class PdoDataAccess
{
    public static function getCryptoDB(): ?PdoConnection
    {
        $databaseName = realpath(\Constants::SITE_ROOT . 'data' . DIRECTORY_SEPARATOR . 'crypto.db');

        $sqlConfig = new PdoConfiguration(ServerType::SQLITE, $databaseName);
        $connection = new PdoConnection($sqlConfig);

        $isFound = (file_exists($databaseName));
        $connection->open();

        if (!$isFound) {
            $connection->exec("CREATE TABLE crypto (id integer primary key autoincrement, token text, userId text, userName text, outdated integer);");
            $connection->exec("CREATE INDEX crypto_id ON crypto (id);");
            $connection->exec("CREATE UNIQUE INDEX covering_idx ON crypto (token, userId, userName, outdated);");
        }

        return $connection;
    }


    public static function getNidusLiteDB(): ?PdoConnection
    {
        $databaseName = APP_DATA . 'niduslite.db';
        $isFound = (file_exists($databaseName));
        $size = 0;
        if ($isFound) {
            $size = filesize($databaseName);
        }
        $sqlConfig = new PdoConfiguration(ServerType::SQLITE, $databaseName);
        $connection = new PdoConnection($sqlConfig);

        $connection->open();

        if (!$isFound || ($isFound && $size === 0)) {
            $sqlFilename = \Constants::EPHECT_APPS_ROOT . 'common' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fxcms.sql';
            if (file_exists($sqlFilename)) {
                $sql = file_get_contents($sqlFilename);
                $connection->exec($sql);
            }
        }

        return $connection;
    }
}
