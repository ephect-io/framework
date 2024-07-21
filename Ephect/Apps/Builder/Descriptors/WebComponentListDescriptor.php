<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Apps\Builder\Descriptors\ComponentListDescriptorInterface;
use Ephect\Framework\Utils\File;

class WebComponentListDescriptor implements ComponentListDescriptorInterface
{

    public function describe(): array
    {
        $result = [];

        $descriptor = new WebComponentDescriptor;
        $webcomponentList = File::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT, ['phtml']);
        foreach ($webcomponentList as $key => $webcomponentFile) {
            [$fqcn, $comp] = $descriptor->describe(CUSTOM_WEBCOMPONENTS_ROOT, $webcomponentFile);
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}