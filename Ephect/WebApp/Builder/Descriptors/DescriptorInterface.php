<?php

namespace Ephect\WebApp\Builder\Descriptors;

interface DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array;

}