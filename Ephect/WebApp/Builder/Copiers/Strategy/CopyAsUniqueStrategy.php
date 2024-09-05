<?php

namespace Ephect\WebApp\Builder\Copiers\Strategy;

use Ephect\Framework\Utils\File;

class CopyAsUniqueStrategy implements CopierStrategyInterface
{
    public function __construct()
    {
        File::safeMkDir(UNIQUE_DIR);
    }

    public function copy(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if ($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(UNIQUE_DIR . $root . $dirname);
        $contents = file_get_contents($path . $dirname . $basename);

        copy($path . $dirname . $basename, UNIQUE_DIR . $root . $dirname . $basename);
    }
}