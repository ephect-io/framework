<?php

namespace Ephect\WebApp\Builder\Parsers;

use Ephect\Forms\Components\ComponentEntity;
use Ephect\Forms\Components\Generators\ComponentParser;
use Ephect\Forms\Registry\CodeRegistry;
use Ephect\Forms\Registry\ComponentRegistry;

class ModuleParser implements ParserTypeInterface
{
    public function __construct(private readonly string $moduleEntrypointClass, private readonly string $filename)
    {
    }

    public function parse(): array
    {
        $comp = new $this->moduleEntrypointClass;
        $comp->load($this->filename);
        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($this->filename, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}