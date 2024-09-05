<?php

namespace Ephect\WebApp\Builder\Copiers;

use Ephect\Framework\Utils\File;
use Ephect\WebApp\Builder\Copiers\Strategy\CopierStrategyInterface;

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