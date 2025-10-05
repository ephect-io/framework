<?php

namespace Ephect\Modules\WebApp\Builder\Parsers;

use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Generators\ComponentParser;

class ModuleParser implements ParserTypeInterface
{
    public function __construct(private readonly string $moduleEntrypointClass, private readonly string $filename)
    {
    }

    public function parse(): array
    {
        $comp = new $this->moduleEntrypointClass();
        $comp->load($this->filename);
        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = new ComponentDeclaration($struct);
        $parser->registerMiddlewares($comp->getFullyQualifiedFunction(), $decl);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($this->filename, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}
