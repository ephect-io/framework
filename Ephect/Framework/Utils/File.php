<?php

namespace Ephect\Framework\Utils;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class File
{
    public static function walkTreeFiltered($path, $filter = [], $noDepth = false): array
    {
        $result = [];

        $l = strlen($path);

        $iterator = null;

        if ($noDepth) {
            $iterator = dir($path);

            while ($file = $iterator->read()) {
                $fi = pathinfo($file);

                if ($fi['basename'] == '.' || $fi['basename'] == '..') {
                    continue;
                }

                if (is_dir($file)) {
                    continue;
                }

                if (isset($fi['extension']) && $fi['extension'] === 'DS_Store') {
                    continue;
                }

                if (
                    (count($filter) > 0 && isset($fi['extension']) && in_array($fi['extension'], $filter))
                    || count($filter) === 0
                ) {
                    $result[] = $file;
                }
            }
        } else {
            $dir_iterator = new RecursiveDirectoryIterator($path, FilesystemIterator::FOLLOW_SYMLINKS);
            $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $file) {
                $fi = pathinfo($file->getPathName());

                if ($fi['basename'] == '.' || $fi['basename'] == '..') {
                    continue;
                }

                if (is_dir($file->getPathName())) {
                    continue;
                }

                if (isset($fi['extension']) && $fi['extension'] === 'DS_Store') {
                    continue;
                }

                if (
                    (count($filter) > 0 && isset($fi['extension']) && in_array($fi['extension'], $filter))
                    || count($filter) === 0
                ) {
                    $result[] = substr($file->getPathName(), $l);
                }
            }
        }

        return $result;
    }

    public static function walkTree(string $path, array &$tree = []): int
    {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
            @array_push($tree, $path) :
            array_map($class_func, glob($path . '/*'), $tree);
    }

    public static function delTree(string $path): bool
    {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
            @unlink($path) :
            array_map($class_func, glob($path . '/*')) == @rmdir($path);
    }

    public static function safeMkDir(string $directory): bool
    {
        if (!$result = file_exists($directory)) {
            $result = mkdir($directory, 0775, true);
        }

        return $result;
    }

    public static function safeRmdir($src): bool
    {
        if (!file_exists($src)) {
            return false;
        }

        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . DIRECTORY_SEPARATOR . $file;
                if (is_dir($full)) {
                    self::safeRmdir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);

        return true;
    }

    public static function safeWrite(string $filename, string $contents): ?int
    {
        $result = null;

        $dir = pathinfo($filename, PATHINFO_DIRNAME);

        if (!file_exists($dir)) {
            $result = mkdir($dir, 0775, true);
        }
        return (false === $len = file_put_contents($filename, $contents)) ? null : $len;
    }

    public static function safeRead(string $filename): ?string
    {
        if (!file_exists($filename)) {
            return null;
        }
        return (false === $contents = file_get_contents($filename)) ? null : $contents;
    }

    public static function safeReadLines(string $filename): ?array
    {
        if (!file_exists($filename)) {
            return null;
        }
        return (false === $contents = file($filename)) ? null : $contents;
    }

    /**
     * Should be replaced by realpath()
     *
     * @param string $path
     * @return string
     */
    public static function reducePath(string $path): string
    {
        $array = explode(DIRECTORY_SEPARATOR, $path);

        $c = count($array);
        $offset = 1;
        for ($i = 0; $i < $c; $i++) {
            if ($array[$i] == '..') {
                unset($array[$i]);
                unset($array[$i - $offset]);
                $offset += 2;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $array);
    }
}
