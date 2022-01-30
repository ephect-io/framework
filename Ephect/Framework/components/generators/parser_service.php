<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\Components\FileComponentInterface;
use Ephect\Framework\Components\Generators\TokenParsers\ArgumentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\ArraysParser;
use Ephect\Framework\Components\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Framework\Components\Generators\TokenParsers\ChildSlotsParser;
use Ephect\Framework\Components\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\EchoParser;
use Ephect\Framework\Components\Generators\TokenParsers\FragmentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\HeredocParser;
use Ephect\Framework\Components\Generators\TokenParsers\MotherSlotsParser;
use Ephect\Framework\Components\Generators\TokenParsers\NamespaceParser;
use Ephect\Framework\Components\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\PhpTagsParser;
use Ephect\Framework\Components\Generators\TokenParsers\UseEffectParser;
use Ephect\Framework\Components\Generators\TokenParsers\UsePropsParser;
use Ephect\Framework\Components\Generators\TokenParsers\UsesAsParser;
use Ephect\Framework\Components\Generators\TokenParsers\UsesParser;
use Ephect\Framework\Components\Generators\TokenParsers\UseVariablesParser;
use Ephect\Framework\Components\Generators\TokenParsers\ValuesParser;
use Ephect\Framework\Registry\ComponentRegistry;

class ParserService implements ParserServiceInterface
{


    protected $component = null;
    protected $useVariables = [];
    protected $useTypes = [];
    protected $html = '';
    protected $children = null;
    protected $componentList = [];
    protected $openComponentList = [];
    protected $result = null;

    public function getVariables(): ?array
    {
        return $this->useVariables;
    }

    public function getUses(): ?array
    {
        return $this->useTypes;
    }

    public function getResult(): null|string|array|bool
    {
        return $this->result;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getChildren(): ?object
    {
        return $this->children;
    }

    public function doArguments(FileComponentInterface $component): void
    {
        $p = new ArgumentsParser($component);
        $p->do();

        $this->result = $p->getResult();
    }

    public function doChildSlots(FileComponentInterface $component): void
    {
        $p = new ChildSlotsParser($component);
        $p->do();
        $this->html = $p->getHtml();
        $this->result = $p->getResult();
    }

    public function doMotherSlots(FileComponentInterface $component): void
    {
        $p = new MotherSlotsParser($component, $this);
        $p->do();
        $this->html = $p->getHtml();
        $this->result = $p->getResult();
    }
    
    public function doUses(FileComponentInterface $component): void
    {
        $p = new UsesParser($component);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function doUsesAs(FileComponentInterface $component): void
    {
        $p = new UsesAsParser($component);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function doHeredoc(FileComponentInterface $component): void
    {
        $p = new HeredocParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doPhpTags(FileComponentInterface $component): void
    {
        $p = new PhpTagsParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doChildrenDeclaration(FileComponentInterface $component): void
    {
        $p = new ChildrenDeclarationParser($component);
        $p->do();
        $this->children = (object) $p->getResult();
    }

    public function doValues(FileComponentInterface $component): void
    {
        $p = new ValuesParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doEchoes(FileComponentInterface $component): void
    {
        $p = new EchoParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doArrays(FileComponentInterface $component): void
    {
        $p = new ArraysParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doUseProps(FileComponentInterface $component): void
    {
        $p = new UsePropsParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }
    
    public function doUseEffect(FileComponentInterface $component): void
    {
        $p = new UseEffectParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doUseSlot(FileComponentInterface $component): void
    {
        $p = new UseSlotParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doUseVariables(FileComponentInterface $component): void
    {
        $p = new UseVariablesParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doNamespace(FileComponentInterface $component): void
    {
        $p = new NamespaceParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doFragments(FileComponentInterface $component): void
    {
        $p = new FragmentsParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doClosedComponents(FileComponentInterface $component): void
    {
        $p = new ClosedComponentsParser($component);
        $p->do();
        $this->componentList = $p->getResult();
        $this->html = $p->getHtml();
    }

    public function doOpenComponents(FileComponentInterface $component): void
    {
        $p = new OpenComponentsParser($component);
        $p->do($this->useVariables);
        $this->openComponentList = $p->getResult();
        $this->html = $p->getHtml();
    }


    public function doIncludes(FileComponentInterface $component): void
    {
        $componentList = array_unique(array_merge($this->componentList, $this->openComponentList));

        ComponentRegistry::uncache();
        $motherUID = $component->getMotherUID();

        foreach ($componentList as $componentName) {
            [$fqFunctionName, $cacheFilename] = $component->renderComponent($motherUID, $componentName);

            $moduleNs = "namespace " . $component->getNamespace() . ';' . PHP_EOL;
            $include = str_replace('%s', $cacheFilename, INCLUDE_PLACEHOLDER);
            $this->html = str_replace($moduleNs, $moduleNs . PHP_EOL . $include, $this->html);

        }
    }
}
