<?php

declare(ticks=1);

namespace Ephect\Framework\CLI\FileSystem;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Utils\File;

use function Ephect\Hooks\useInterval;

class Watcher
{
    public function watch(string $directory, array $filter): void
    {
        $mtimes = [];

        useInterval(function () use ($directory, $filter, &$mtimes) {
            $files = $this->_listFiles($directory, $filter);

            foreach ($files as $filename) {

                $mtime = filemtime($directory . $filename);

                if (isset($mtimes[$filename]) && $mtimes[$filename] < $mtime) {
                    Console::writeLine('File "%s" was modified', $directory . $filename);
                }
                $mtimes[$filename] = $mtime;

            }

        }, 100);

        while (true) {
            usleep(1);
        }

    }

    private function _listFiles(string $directory, array $filter): array
    {
        return File::walkTreeFiltered($directory, $filter);
    }
}
