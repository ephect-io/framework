<?php

namespace Ephect\Modules\WebApp\Commands\Build;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Modules\WebApp\Builder\Builder;

class Lib extends AbstractCommandLib
{
    public function build(): void
    {
        $application = $this->parent;

        $application->clearRuntime();
        $application->clearLogs();

        $builder = new Builder();
        $builder->describeComponents();
        $builder->preparePagesList();
        $builder->prepareRoutedComponents();

        $builder->buildAllRoutes();
    }
}
