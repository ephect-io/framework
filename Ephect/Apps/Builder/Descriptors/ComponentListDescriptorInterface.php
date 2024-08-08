<?php

namespace Ephect\Apps\Builder\Descriptors;

interface ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array;

}