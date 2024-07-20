<?php

namespace Ephect\Commands\Build;

use Ephect\Apps\Builder\Builder;
use Ephect\Framework\Commands\AbstractCommandLib;

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

        $builder->buildAllRoutes();
    }
}

