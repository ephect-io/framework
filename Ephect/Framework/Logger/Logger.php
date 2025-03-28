<?php

namespace Ephect\Framework\Logger;

use Ephect\Framework\Utils\Text;
use ErrorException;
use Throwable;

use function file_exists;
use function file_get_contents;

class Logger
{
    private static Logger|null $logger = null;

    private function __construct()
    {
    }

    public static function create(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger();
        }
        return self::$logger;
    }

    public function dump(string $message, object|array $object): void
    {
        $this->debug($message . '::' . print_r($object, true) . PHP_EOL);
    }

    public function debug(string|array|object $message, string $filename = '', int $line = -1): void
    {
        $this->__log(\Constants::DEBUG_LOG, $message, $filename, $line);
    }

    private function __log(string $filepath, string|array|object $message, string $filename = '', int $line = -1): void
    {
        $message = (is_array($message) || is_object($message)) ? print_r($message, true) : $message;

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755);
        }

        $handle = fopen($filepath, 'a');

        if (\Constants::SRC_ROOT) {
            $filename = substr($filename, strlen(\Constants::SRC_ROOT));
        }
        $message = date('Y-m-d h:i:s') . (isset($filename) ? ":$filename" : '') . ($line > -1 ? ":$line" : '') . " : $message" . PHP_EOL;
        fwrite($handle, $message . PHP_EOL);
        fclose($handle);
    }

    public function info(string $string, ...$params): void
    {
        $message = Text::format($string, $params);
        $this->__log(\Constants::INFO_LOG, $message);
    }

    public function sql(string|array|object $message, string $filename = '', int $line = -1): void
    {
        $this->__log(\Constants::SQL_LOG, $message, $filename, $line);
    }

    public function error(Throwable $ex, string $filename = '', int $line = -1): void
    {
        $message = '';

        if ($ex instanceof ErrorException) {
            $message .= 'Error severity: ' . $ex->getSeverity() . PHP_EOL;
        }
        $message .= 'Error code: ' . $ex->getCode() . PHP_EOL;
        $message .= 'In ' . $ex->getFile() . ', line ' . $ex->getLine() . PHP_EOL;
        $message .= 'With the message: ' . $ex->getMessage() . PHP_EOL;
        $message .= 'Stack trace: ' . $ex->getTraceAsString() . PHP_EOL;

        $this->__log(\Constants::ERROR_LOG, $message, $filename, $line);
    }

    public function getInfoLog(): string
    {
        if (!file_exists(\Constants::INFO_LOG)) {
            return '';
        }
        return file_get_contents(\Constants::INFO_LOG);
    }

    public function getDebugLog(): string
    {
        if (!file_exists(\Constants::DEBUG_LOG)) {
            return '';
        }
        return file_get_contents(\Constants::DEBUG_LOG);
    }

    public function getErrorLog(): string
    {
        if (!file_exists(\Constants::ERROR_LOG)) {
            return '';
        }
        return file_get_contents(\Constants::ERROR_LOG);
    }

    public function getSqlLog(): string
    {
        if (!file_exists(\Constants::SQL_LOG)) {
            return '';
        }
        return file_get_contents(\Constants::SQL_LOG);
    }

    public function getPhpErrorLog(): string
    {
        if (!file_exists(\Constants::DOCUMENT_ROOT . 'php_errors.log')) {
            return '';
        }
        return file_get_contents(\Constants::DOCUMENT_ROOT . 'php_errors.log');
    }

    public function clearAll(): void
    {
        if (file_exists(\Constants::INFO_LOG)) {
            unlink(\Constants::INFO_LOG);
        }

        if (file_exists(\Constants::DEBUG_LOG)) {
            unlink(\Constants::DEBUG_LOG);
        }

        if (file_exists(\Constants::ERROR_LOG)) {
            unlink(\Constants::ERROR_LOG);
        }

        if (file_exists(\Constants::SQL_LOG)) {
            unlink(\Constants::SQL_LOG);
        }

        if (file_exists(\Constants::DOCUMENT_ROOT . 'php_errors.log')) {
            unlink(\Constants::DOCUMENT_ROOT . 'php_errors.log');
        }
    }
}
