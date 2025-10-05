<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;

class UniqueComponentListDescriptor implements ComponentListDescriptorInterface
{
    public function __construct(protected string $buildDirectory)
    {
    }

    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new UniqueComponentDescriptor($this->buildDirectory);

        $bootstrapList = File::walkTreeFiltered($this->buildDirectory, ['php'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe($this->buildDirectory, $compFile);
            if ($fqcn === null) {
                continue;
            }
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}
