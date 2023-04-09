<?php

namespace Ephect\Commands\Build;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Core\Builder;
use Ephect\Framework\IO\Utils;

class Lib extends AbstractCommandLib
{

    public function build(): void
    {
        $application = $this->parent;
        
        $application->clearRuntime();
        $application->clearLogs();

        $builder = new Builder;
        $builder->describeComponents();
        $builder->prepareRoutedComponents();

        // $compiler->performAgain();

        $builder->buildAllRoutes();

//        $builder->buildWebcomponents();
    }
}

