<?php

namespace Ephect\WebApp\Builder\Descriptors;

interface ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array;

}