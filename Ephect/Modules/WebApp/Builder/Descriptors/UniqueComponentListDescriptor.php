<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Modules\ModuleManifestReader;
use Ephect\Framework\Utils\File;
use function siteSrcPath;

class UniqueComponentListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new UniqueComponentDescriptor();

        $bootstrapList = File::walkTreeFiltered(UNIQUE_DIR, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(UNIQUE_DIR, $compFile);
            if ($fqcn === null) {
                continue;
            }
            $result[$fqcn] = $comp;
        }

        return $result;
    }
}
