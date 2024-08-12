<?php

namespace Ephect\Apps\Builder\Copiers;

use Ephect\Apps\Builder\Copiers\Strategy\CopierStrategyInterface;
use Ephect\Framework\Utils\File;

class TemplatesCopier
{
    public static function copy(CopierStrategyInterface $copier, string $path, bool $noDepth = false): void
    {
        $fileList = File::walkTreeFiltered($path, ['phtml'], $noDepth);
        foreach ($fileList as $key => $compFile) {
            $copier->copy($path, $key, $compFile);
        }
    }
}