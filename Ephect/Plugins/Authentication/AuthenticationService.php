<?php

namespace Ephect\Plugins\Authentication;

use Ephect\Framework\StaticElement;
use Ephect\Plugins\DBAL\TDataAccess;

class AuthenticationService extends StaticElement
{

    protected $userId = null;
    protected $userName = null;

    public function __construct()
    {

    }

    public function getUserId(): string
    {
        if (!isset($this->userId) || empty($this->userId)) {
            if (isset($_SESSION['userId'])) {
                $this->userId = $_SESSION['userId'];
            } else {
                $this->setUserId('#!user' . uniqid() . '#');
            }
        }
        return $this->userId;
    }

    public function setUserId($value): void
    {
        $_SESSION['userId'] = $value;
        $this->userId = $value;
    }

    public function getUserName(): string
    {
        if (!isset($this->userName) || empty($this->userName)) {
            if (isset($_SESSION['userName'])) {
                $this->userName = $_SESSION['userName'];
            } else {
                $this->setUserName('#!user' . uniqid() . '#');
            }
        }
        return $this->userName;
    }

    public function setUserName($value): void
    {
        $_SESSION['userName'] = $value;
        $this->userName = $value;
    }

    public static function getPermissionByToken(string $token): ?string
    {

        $result = null;

        if ($token != '') {

            $token = self::renewToken($token);
            if (is_string($token)) {
                $result = $token;
            }
        }

        return $result;
    }

    public static function setUserToken(string $userId, string $login): ?string
    {
        $result = null;

        $connection = TDataAccess::getCryptoDB();
        $token = TCrypto::generateToken('');
        $stmt = $connection->query(
            "INSERT INTO crypto (token, userId, userName, outdated) VALUES(:token, :userId, :login, 0);"
            , ['token' => $token, 'userId' => $userId, 'login' => $login]
        );

        return ($token || $stmt->fetch()) ? $token : $result;
    }

    public function updateToken($token): ?int
    {
        $result = null;

        $connection = TDataAccess::getCryptoDB();
        $stmt = $connection->query("select * from crypto where token=:token and outdated=0;", ['token' => $token]);

        if ($stmt->fetch()) {
            $stmt = $connection->query("UPDATE crypto SET outdated=1 WHERE token=:token;", ['token' => $token]);
            if ($stmt->fetch()) {
                $result = $stmt->getRowCount();
            }
        }

        return $result;
    }

    public function renewToken($token = ''): ?string
    {
        $result = null;

        self::$logger->debug(__METHOD__ . '::TOKEN::' . $token);

        if (strlen($token) > 0 && substr($token, 0, 1) == '!') {
            $result = $token;
            return $result;
        }

        $userId = $this->getUserId();
        $login = $this->getUserName();

        $connection = TDataAccess::getCryptoDB();
        $stmt = $connection->query("select * from crypto where token =:token and outdated=0;", ['token' => $token]);
        if ($row = $stmt->fetchAssoc()) {

            $userId = $row["userId"];
            $login = $row["userName"];

            $stmt = $connection->query("UPDATE crypto SET outdated=1 WHERE token =:token;", ['token' => $token]);

        }

        $token = TCrypto::generateToken('');
        $connection->query(
            "INSERT INTO crypto (token, userId, userName, outdated) VALUES(:token, :userId, :login, 0);"
            , ['token' => $token, 'userId' => $userId, 'login' => $login]
        );

        $result = $token;

        return $result;
    }

}