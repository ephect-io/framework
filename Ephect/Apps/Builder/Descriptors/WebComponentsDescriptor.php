<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Framework\Components\ComponentEntity;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\WebComponent;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\Utils\File;

class WebComponentsDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $comp = new WebComponent();
        $comp->load($filename);
        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        WebComponentRegistry::write($filename, $uid);
        WebComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}