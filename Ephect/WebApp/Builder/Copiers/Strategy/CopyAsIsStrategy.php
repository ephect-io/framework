<?php

namespace Ephect\WebApp\Builder\Copiers\Strategy;

use Ephect\Framework\Utils\File;

class CopyAsIsStrategy implements CopierStrategyInterface
{
    public function __construct()
    {
        File::safeMkDir(COPY_DIR);
    }

    public function copy(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if ($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(COPY_DIR . $root . $dirname);
        copy($path . $dirname . $basename, COPY_DIR . $root . $dirname . $basename);
    }
}