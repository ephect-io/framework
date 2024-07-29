<?php

namespace Ephect\Apps\Builder\Descriptors;

interface DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array;

}