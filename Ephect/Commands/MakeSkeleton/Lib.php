<?php

namespace Ephect\Commands\BuildWebcomponent;

use Ephect\Framework\IO\Utils;

class Lib
{

    public function createSkeleton(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Skeleton';

        Utils::safeMkDir(SRC_ROOT);
        $destDir = realpath(SRC_ROOT);

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }

        $tree = Utils::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}

