<?php

namespace Ephect\Apps\Builder\Modules;

interface ModuleBuilderInterface
{
    public function describeComponents(array &$list): void;
}