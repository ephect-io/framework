<?php

namespace Ephect\Commands\Build;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Core\Builder;
use Ephect\Framework\IO\Utils;

class Lib extends AbstractCommandLib
{

    public function build(): void
    {
        if (file_exists(CACHE_DIR)) {
            Utils::delTree(CACHE_DIR);
        }

        $builder = new Builder;
        $builder->describeComponents();
        $builder->prepareRoutedComponents();

        // $compiler->performAgain();

        // $motherUID = $builder->buildAllRoutes();
        // $builder->buildWebcomponents($motherUID);
    }
}

