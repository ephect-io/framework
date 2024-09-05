<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Framework\Utils\File;

class ModuleListDescriptor implements ComponentListDescriptorInterface
{

    public function __construct(private string $modulePath)
    {
    }

    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new ModuleDescriptor($this->modulePath);
        $moduleTemplateList = File::walkTreeFiltered($templateDir, ['phtml']);
        foreach ($moduleTemplateList as $key => $moduleTemplate) {
            [$fqcn, $comp] = $descriptor->describe($templateDir, $moduleTemplate);
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}