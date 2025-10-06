<?php

namespace Ephect\Modules\WebApp\Commands\Build;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Modules\WebApp\Builder\Builder;
use Ephect\Modules\WebApp\Services\BuildService;

class Lib extends AbstractCommandLib
{
    public function build(): void
    {
        $application = $this->parent;

        $application->clearRuntime();
        $application->clearLogs();

        $service = new BuildService();
        $builder = $service->build();

        $builder->buildAllRoutes();

    }
}
