<?php
namespace FunCom\IO;

class Utils
{
    public static function walkTree($path, $filter = [])
    {
        $result = [];
        
        $l = strlen($path);
        
        $dir_iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            $fi = pathinfo($file->getPathName());
            
            if ($fi['basename'] ==  '.' || $fi['basename'] == '..') {
                continue;
            }
            
            if (isset($fi['extension'])) {
                if (count($filter) > 0 && in_array($fi['extension'], $filter)) {
                    array_push($result, substr($file->getPathName(), $l));
                } elseif (count($filter) === 0) {
                    array_push($result, substr($file->getPathName(), $l));
                }
            }
        }

        return $result;
    }

    public static function walkTree2(string $path, ?array &$tree)
    {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
                @array_push($tree, $path) :
                array_map($class_func, glob($path.'/*'), $tree);
    }

    public static function delTree(string $path) : bool
    {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
                @unlink($path) :
                array_map($class_func, glob($path.'/*')) == @rmdir($path);
    }

    public static function safeWrite(string $filename, string $contents): ?string
    {
        $result = null;

        $dir = pathinfo($filename, PATHINFO_DIRNAME);

        if(!file_exists($dir)) {
            $result = mkdir($dir, 0775, true);
        }
        $result = (false === file_put_contents($filename, $contents)) ? null : $filename;

        return $result;
    }
}