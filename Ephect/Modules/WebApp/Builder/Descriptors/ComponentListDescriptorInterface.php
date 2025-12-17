<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

interface ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array;
}
