<?php

namespace Ephect\Framework\Components\Application;

use Ephect\Framework\Components\Component;
use Ephect\Framework\Components\FileComponentInterface;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\Generators\ParserService;
use Ephect\Framework\ElementTrait;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Utils\File;
use Exception;

class ApplicationRecursiveParser
{
    use ElementTrait;
    use ComponentCodeTrait;

    public function __construct(private FileComponentInterface $component)
    {
        $this->motherUID = $this->component->getMotherUID();
    }

    /**
     * @throws Exception
     */
    public function parse(): void
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->getMotherUID());
        CodeRegistry::load();

        $parser = new ParserService();

        $parser->doUses($this->component);
        $parser->doUsesAs($this->component);

        $parser->doHeredoc($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doInlineCode($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doChildrenDeclaration($this->component);

        $parser->doArrays($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doUseEffect($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doReturnType($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doModuleComponent($this->component);

        $parser->doUseVariables($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doNamespace($this->component);
        $this->component->applyCode($parser->getHtml());

        $parser->doFragments($this->component);
        $this->component->applyCode($parser->getHtml());

//        $filename = $this->getFlattenSourceFilename();
        $filename = $this->component->getSourceFilename();
        File::safeWrite(CACHE_DIR . $this->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $this->component->getCode());
        $this->updateComponent($this->component);

        $parser->doChildSlots($this->component);
        $this->component->applyCode($parser->getHtml());
        $this->updateComponent($this->component);

        while ($compz = $this->component->getDeclaration()->getComposition() !== null) {
            $parser->doOpenComponents($this->component);
            $this->component->applyCode($parser->getHtml());
            $this->updateComponent($this->component);

            $parser->doClosedComponents($this->component);
            $this->component->applyCode($parser->getHtml());
            $this->updateComponent($this->component);

            $parser->doIncludes($this->component);
            $this->component->applyCode($parser->getHtml());
        }

//        $this->code = $this->component->getCode();
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