<?php

namespace Ephect\Modules\WebApp\Services;

use Ephect\Modules\WebApp\Builder\Builder;

class BuildService
{
    public function build(): Builder
    {
        $builder = new Builder();
        $builder->describeComponents();
        $builder->preparePagesList();
        $builder->prepareRoutedComponents();

        return $builder;
    }
}