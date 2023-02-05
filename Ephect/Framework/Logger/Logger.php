<?php

namespace Ephect\Framework\Logger;

use Ephect\Framework\Utils\TextUtils;

class Logger
{
    private static $_logger = null;

    private function __construct()
    {
    }

    public static function create(): Logger
    {
        if (self::$_logger === null) {
            self::$_logger = new Logger();
        }
        return self::$_logger;
    }

    public function dump(string $message, object|array $object)
    {
        $this->debug($message . '::' . print_r($object, true) . PHP_EOL);
    }

    public function info(string $string, ...$params): void
    {
        $message = TextUtils::format($string, $params);
        $this->_log(INFO_LOG, $message);
    }


    public function debug(string|array|object $message, string $filename = '', int $line = -1): void
    {
        $this->_log(DEBUG_LOG, $message, $filename, $line);
    }

    public function sql(string|array|object $message, string $filename = '', int $line = -1): void
    {
        $this->_log(SQL_LOG, $message, $filename, $line);
    }

    public function error(\Throwable $ex,  string $filename = '', int $line = -1): void
    {
        $message = '';

        if ($ex instanceof \ErrorException) {
            $message .= 'Error severity: ' . $ex->getSeverity() . PHP_EOL;
        }
        $message .= 'Error code: ' . $ex->getCode() . PHP_EOL;
        $message .= 'In ' . $ex->getFile() . ', line ' . $ex->getLine() . PHP_EOL;
        $message .= 'With the message: ' . $ex->getMessage() . PHP_EOL;
        $message .= 'Stack trace: ' . $ex->getTraceAsString() . PHP_EOL;

        $this->_log(ERROR_LOG, $message, $filename, $line);
    }

    private function _log(string $filepath, string|array|object $message, string $filename = '', int $line = -1): void
    {
        $message = (is_array($message) || is_object($message)) ? print_r($message, true) : $message;

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755);
        }

        $handle = fopen($filepath, 'a');

        if (SRC_ROOT) {
            $filename = substr($filename, strlen(SRC_ROOT));
        }
        $message = date('Y-m-d h:i:s') . (isset($filename) ? ":$filename" : '') . ($line > -1 ? ":$line" : '') . " : $message" . PHP_EOL;
        fwrite($handle, $message . PHP_EOL);
        fclose($handle);
    }

    public function getInfoLog(): string
    {
        if (!\file_exists(INFO_LOG)) {
            return '';
        }
        return \file_get_contents(INFO_LOG);
    }

    public function getDebugLog(): string
    {
        if (!\file_exists(DEBUG_LOG)) {
            return '';
        }
        return \file_get_contents(DEBUG_LOG);
    }

    public function getErrorLog(): string
    {
        if (!\file_exists(ERROR_LOG)) {
            return '';
        }
        return \file_get_contents(ERROR_LOG);
    }

    public function getSqlLog(): string
    {
        if (!\file_exists(SQL_LOG)) {
            return '';
        }
        return \file_get_contents(SQL_LOG);
    }

    public function getPhpErrorLog(): string
    {
        if (!\file_exists(DOCUMENT_ROOT . 'php_error_log')) {
            return '';
        }
        return \file_get_contents(DOCUMENT_ROOT . 'php_error_log');
    }

    public function clearAll(): void
    {
        if (file_exists(INFO_LOG)) {
            unlink(INFO_LOG);
        }

        if (file_exists(DEBUG_LOG)) {
            unlink(DEBUG_LOG);
        }

        if (file_exists(ERROR_LOG)) {
            unlink(ERROR_LOG);
        }

        if (file_exists(SQL_LOG)) {
            unlink(SQL_LOG);
        }

        if (file_exists(DOCUMENT_ROOT . 'php_error_log')) {
            unlink(DOCUMENT_ROOT . 'php_error_log');
        }
    }
}
