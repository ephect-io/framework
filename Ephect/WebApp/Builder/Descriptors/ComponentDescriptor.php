<?php

namespace Ephect\WebApp\Builder\Descriptors;

use Ephect\Forms\Components\Component;
use Ephect\Forms\Components\ComponentEntity;
use Ephect\Forms\Components\Generators\ComponentParser;
use Ephect\Forms\Components\Generators\ParserService;
use Ephect\Forms\Registry\CodeRegistry;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Framework\Utils\File;

class ComponentDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $comp = new Component();
        $comp->load($filename);

        $parser = new ParserService;
        $parser->doEmptyComponents($comp);
        if ($parser->getResult() === true) {
            $html = $parser->getHtml();
            File::safeWrite(COPY_DIR . $filename, $html);
            $comp->load($filename);
        }

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