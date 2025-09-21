<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Generators\ComponentParser;
use Ephect\Modules\Forms\Generators\ParserService;

class ComponentDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        $relativeFile =
            str_replace(\Constants::APP_ROOT, '', $sourceDir) .
            str_replace(pathinfo($filename, PATHINFO_EXTENSION), 'php', $filename);
        File::safeCopy($sourceDir . $filename, \Constants::COPY_DIR . $relativeFile);

        $comp = new Component();
        $comp->load($relativeFile);

        $parser = new ParserService();
        $parser->doEmptyComponents($comp);
        if ($parser->getResult() === true) {
            $html = $parser->getHtml();
            File::safeWrite(\Constants::COPY_DIR . $relativeFile, $html);
            $comp->load($relativeFile);
        }

        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($relativeFile, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}