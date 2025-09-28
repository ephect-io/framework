<?php

namespace Ephect\Commands\MakeQuickStart;

use Ephect\Framework\Utils\File;

class Lib
{

    public function createQuickstart(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'QuickStart';

        File::safeMkDir(SRC_ROOT);
        $destDir = realpath(SRC_ROOT);

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }

        $tree = File::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            File::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}

