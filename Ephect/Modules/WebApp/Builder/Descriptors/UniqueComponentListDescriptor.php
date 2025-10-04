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

        $bootstrapList = File::walkTreeFiltered(\Constants::UNIQUE_DIR, ['php'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(\Constants::UNIQUE_DIR, $compFile);
            if ($fqcn === null) {
                continue;
            }
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}
