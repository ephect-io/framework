<?php

namespace Ephect\Modules\WebApp\Builder\Copiers\Strategy;

use Ephect\Framework\Utils\File;

class CopyAsIsStrategy implements CopierStrategyInterface
{
    public function __construct()
    {
        File::safeMkDir(\Constants::COPY_DIR);
    }

    public function copy(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;
        $stdFile = str_replace(pathinfo($filename, PATHINFO_EXTENSION), 'php', $filename);

        if ($root === \Constants::APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($stdFile, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($stdFile, PATHINFO_BASENAME);
        File::safeMkDir(\Constants::COPY_DIR . $root . $dirname);
        copy($path . $dirname . $basename, \Constants::COPY_DIR . $root . $dirname . $basename);
    }
}
