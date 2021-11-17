<?php

namespace Ephect\Cache;

use Ephect\IO\Utils;
use Ephect\StaticElement;

class Cache extends StaticElement
{

    public static function getCacheFilename(string $basename): string
    {
        $cache_file = CACHE_DIR . str_replace('/', '_', $basename);

        return $cache_file;
    }
    
    public static function cacheFilenameFromView(string $compName): string
    {

        // $uri = bin2hex(REQUEST_URI);
        $uri = '';
        return REL_RUNTIME_DIR . strtolower($compName) . $uri . CLASS_EXTENSION;
    }

    public static function absoluteURL(string $relativeURL = ''): string
    {
        return ((HTTP_HOST !== SERVER_NAME) ? SERVER_HOST : SERVER_ROOT) . REWRITE_BASE . $relativeURL;
    }

    public static function cachePath(string $filepath): string
    {
        return  str_replace(DIRECTORY_SEPARATOR, '_', $filepath);
    }

    public static function cacheFile($filename, $content): void
    {
        $filename = CACHE_DIR . $filename;
        file_put_contents($filename, $content);
    }

    public static function createRuntimeDirs(): bool
    {
        $result = false;
        $error_dir = [];

        try {
            $runtime_dir = dirname(RUNTIME_DIR . '_');
            if (!file_exists($runtime_dir)) {
                $ok = mkdir($runtime_dir, 0755, true);
                $result = $result || $ok;
            }
            if (!file_exists($runtime_dir)) {
                $error_dir[] = RUNTIME_DIR;
            }

            if (count($error_dir) > 0) {
                $result = false;

                $message = 'An error occured while creating ' . implode(', ', $error_dir);
                throw new \Exception($message, 0);
            }
        } catch (\Throwable $ex) {
            self::getLogger()->error($ex);
        }

        return $result;
    }

    public static function deleteRuntimeDirs(): bool
    {
        $result = false;
        $error_dir = [];

        try {

            if (file_exists(RUNTIME_DIR)) {
                $ok = Utils::delTree(RUNTIME_DIR);
                $result = $result || $ok;
            } else {
                $error_dir[] = RUNTIME_DIR;
            }

            if (count($error_dir) > 0) {
                $result = false;

                $message = 'An error occured while deleting ' . implode(', ', $error_dir);
                throw new \Exception($message, 0);
            }
        } catch (\Exception $ex) {
            self::getLogger()->error($ex);
        }

        return $result;
    }

    public static function clearRuntime(): bool
    {
        $result = false;
        try {
            $result = $result || self::deleteRuntimeDirs();
            $result = $result || self::createRuntimeDirs();
            $result = $result || self::deleteCacheDir();
            $result = $result || self::createCacheDir();

        } catch (\Throwable $ex) {
            self::getLogger()->error($ex);

            $result = false;
        }
        return $result;
    }


    public static function clearCache(): bool
    {
        $result = false;
        try {
            $result = $result || self::deleteCacheDir();
            $result = $result || self::createCacheDir();

        } catch (\Throwable $ex) {
            self::getLogger()->error($ex);

            $result = false;
        }
        return $result;
    }

    public static function createCacheDir(): bool
    {
        $result = false;
        $error_dir = [];

        try {
            $cache_dir = dirname(CACHE_DIR . '_');
            if (!file_exists($cache_dir)) {
                $ok = mkdir($cache_dir, 0755, true);
                $result = $result || $ok;
            }
            if (!file_exists($cache_dir)) {
                $error_dir[] = CACHE_DIR;
            }

            if (count($error_dir) > 0) {
                $result = false;

                $message = 'An error occured while creating ' . implode(', ', $error_dir);
                throw new \Exception($message, 0);
            }
        } catch (\Throwable $ex) {
            self::getLogger()->error($ex);
        }

        return $result;
    }

    public static function deleteCacheDir(): bool
    {
        $result = false;
        $error_dir = [];

        try {

            if (file_exists(CACHE_DIR)) {
                $ok = Utils::delTree(CACHE_DIR);
                $result = $result || $ok;
            } else {
                $error_dir[] = CACHE_DIR;
            }

            if (count($error_dir) > 0) {
                $result = false;

                $message = 'Permission denied while deleting ' . implode(', ', $error_dir);
                throw new \Exception($message, 0);
            }
        } catch (\Exception $ex) {
            self::getLogger()->error($ex);
        }

        return $result;
    }

}
