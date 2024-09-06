<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentInterface;

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
            if (is_string($fqcn) && $comp instanceof ComponentInterface) {
                $result[$fqcn] = $comp;
            }
        }

        return $result;
    }
}