<?php

namespace Ephect\Framework\Cache;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\StaticElement;
use Ephect\Framework\Utils\File;
use Throwable;

class Cache extends StaticElement
{

    public static function getCacheFilename(string $basename): string
    {
        return CACHE_DIR . str_replace('/', '_', $basename);
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
        return str_replace(DIRECTORY_SEPARATOR, '_', $filepath);
    }

    public static function cacheFile($filename, $content): void
    {
        $filename = CACHE_DIR . $filename;
        file_put_contents($filename, $content);
    }

    public static function clearRuntime(): bool
    {
        $result = false;
        try {
            $result &= self::clearCache();
            $result &= self::clearRuntimeDirs();
            $result &= self::clearRuntimeJsDirs();
        } catch (Throwable $ex) {
            Console::error($ex);

            $result = false;
        }
        return $result;
    }

    public static function clearCache(): bool
    {
        $result = false;
        if (file_exists(CACHE_DIR)) {
            $result &= File::delTree(CACHE_DIR);
        }

        return $result;
    }

    public static function clearRuntimeDirs(): bool
    {
        $result = false;
        if (file_exists(RUNTIME_DIR)) {
            $result &= File::delTree(RUNTIME_DIR);
        }
        return $result;
    }

    public static function clearRuntimeJsDirs(): bool
    {
        $result = false;
        if (file_exists(RUNTIME_JS_DIR)) {
            $result &= File::delTree(RUNTIME_JS_DIR);
        }
        return $result;
    }
}
