<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Framework\Components\ComponentEntity;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Utils\File;

class ModuleDescriptor implements DescriptorInterface
{
    public function __construct(private string $modulePath)
    {
    }

    public function describe(string $sourceDir, string $filename): array
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        //TODO: get module class from module middleware
        $moduleConfigDir = $this->modulePath . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;
        $moduleEntrypointFile = $moduleConfigDir . 'entrypoint.php';
        $moduleEntrypoint = file_exists($moduleEntrypointFile) ? require_once $moduleEntrypointFile : null;

        if($moduleEntrypoint == null) {
            throw new \Exception("Module entry point not found in {$moduleEntrypointFile}");
        }

        $comp = new $moduleEntrypoint;
        $comp->load($filename);
        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($filename, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}