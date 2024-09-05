<?php

namespace Ephect\Modules\Forms\Application;

use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Generators\ComponentParser;

abstract class AbstractApplicationParser
{
    use ComponentCodeTrait;

    /**
     * @return void
     */
    abstract protected function __parse(FileComponentInterface $component): void;

    public function updateComponent(FileComponentInterface $component): string
    {
        $uid = $component->getUID();
        $motherUID = $component->getMotherUID();
        $filename = $component->getSourceFilename();

        $comp = new Component($uid, $motherUID);
        $comp->load($filename);
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        CodeRegistry::save();

        return $filename;
    }
}
