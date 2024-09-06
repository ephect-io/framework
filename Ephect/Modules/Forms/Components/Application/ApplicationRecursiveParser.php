<?php

namespace Ephect\Modules\Forms\Components\Application;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Components\Generators\ComponentParser;
use Ephect\Modules\Forms\Components\Generators\ParserService;
use Ephect\Modules\Forms\Registry\CodeRegistry;

class ApplicationRecursiveParser
{
    use ComponentCodeTrait;

    /**
     * @return void
     */
    public static function parse(FileComponentInterface $component): void
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $component->getMotherUID());
        CodeRegistry::load();

        $parser = new ParserService();

        $parser->doUses($component);
        $parser->doUsesAs($component);

        $parser->doHeredoc($component);
        $component->applyCode($parser->getHtml());

        $parser->doInlineCode($component);
        $component->applyCode($parser->getHtml());

        $parser->doChildrenDeclaration($component);

        $parser->doArrays($component);
        $component->applyCode($parser->getHtml());

        $parser->doUseEffect($component);
        $component->applyCode($parser->getHtml());

        $parser->doReturnType($component);
        $component->applyCode($parser->getHtml());

        $parser->doModuleComponent($component);

        $parser->doUseVariables($component);
        $component->applyCode($parser->getHtml());

        $parser->doNamespace($component);
        $component->applyCode($parser->getHtml());

        $parser->doFragments($component);
        $component->applyCode($parser->getHtml());

        $filename = $component->getSourceFilename();
        File::safeWrite(CACHE_DIR . $component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $component->getCode());
        self::updateComponent($component);

        $parser->doChildSlots($component);
        $component->applyCode($parser->getHtml());
        self::updateComponent($component);

        while ($compz = $component->getDeclaration()->getComposition() !== null) {
            $parser->doOpenComponents($component);
            $component->applyCode($parser->getHtml());
            self::updateComponent($component);

            $parser->doClosedComponents($component);
            $component->applyCode($parser->getHtml());
            self::updateComponent($component);

            $parser->doIncludes($component);
            $component->applyCode($parser->getHtml());
        }

        CodeRegistry::save();
    }

    public static function updateComponent(FileComponentInterface $component): string
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