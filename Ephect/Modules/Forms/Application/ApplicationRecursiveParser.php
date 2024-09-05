<?php

namespace Ephect\Modules\Forms\Application;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Generators\ParserService;

class ApplicationRecursiveParser extends AbstractApplicationParser
{
    use ComponentCodeTrait;

    public static function parse(FileComponentInterface $component): void
    {
        $parser = new self();
        $parser->__parse($component);
    }

    /**
     * @param FileComponentInterface $component
     * @return void
     */
    protected function __parse(FileComponentInterface $component): void
    {
        CodeRegistry::setCacheDirectory(\Constants::CACHE_DIR . $component->getMotherUID());
        CodeRegistry::load();

        $parser = new ParserService();

        $parser->doAttributes($component);

//        $parser->doUses($component);
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
        File::safeWrite(
            \Constants::CACHE_DIR . $component->getMotherUID() . DIRECTORY_SEPARATOR . $filename,
            $component->getCode()
        );
        $this->updateComponent($component);

        $parser->doChildSlots($component);
        $component->applyCode($parser->getHtml());
        $this->updateComponent($component);

        while ($component->getDeclaration()?->getComposition() !== null) {
            $parser->doOpenComponents($component);
            $component->applyCode($parser->getHtml());
            $this->updateComponent($component);

            $parser->doClosedComponents($component);
            $component->applyCode($parser->getHtml());
            $this->updateComponent($component);

            $parser->doIncludes($component);
            $component->applyCode($parser->getHtml());
        }

        CodeRegistry::save();
    }
}
