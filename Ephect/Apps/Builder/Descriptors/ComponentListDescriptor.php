<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Framework\Utils\File;

class ComponentListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(): array
    {
        $result = [];

        $descriptor = new ComponentDescriptor;

        $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(SRC_ROOT, $compFile);
            $result[$fqcn] = $comp;
        }

        $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            [$fqcn, $comp] = $descriptor->describe(CUSTOM_PAGES_ROOT, $pageFile);
            $result[$fqcn] = $comp;
        }

        $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
        foreach ($componentsList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(CUSTOM_COMPONENTS_ROOT, $compFile);
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}