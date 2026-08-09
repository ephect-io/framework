<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Modules\ModuleManifestReader;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Registry\UniqueCodeRegistry;

use function siteSrcPath;

class ComponentListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new ComponentDescriptor();

        $componentsList = File::walkTreeFiltered(\Constants::COPY_COMPONENTS_ROOT, ['phtml']);
        foreach ($componentsList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(\Constants::COPY_COMPONENTS_ROOT, $compFile);
            $result[$fqcn] = $comp;
        }

        UniqueCodeRegistry::save();
        UniqueCodeRegistry::load();

        $pagesList = File::walkTreeFiltered(\Constants::COPY_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            [$fqcn, $comp] = $descriptor->describe(\Constants::COPY_PAGES_ROOT, $pageFile);
            $result[$fqcn] = $comp;
        }

        UniqueCodeRegistry::save();
        UniqueCodeRegistry::load();

        $bootstrapList = File::walkTreeFiltered(\Constants::COPY_DIR, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(\Constants::COPY_DIR, $compFile);
            $result[$fqcn] = $comp;
        }

        UniqueCodeRegistry::save();

        return $result;
    }
}
