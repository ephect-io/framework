<?php

namespace Ephect\Components\Generators;

use Ephect\Components\FileComponentInterface;
use Ephect\Components\Generators\TokenParsers\ArgumentsParser;
use Ephect\Components\Generators\TokenParsers\ArraysParser;
use Ephect\Components\Generators\TokenParsers\BlocksParser;
use Ephect\Components\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Components\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Components\Generators\TokenParsers\EchoParser;
use Ephect\Components\Generators\TokenParsers\FragmentsParser;
use Ephect\Components\Generators\TokenParsers\NamespaceParser;
use Ephect\Components\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Components\Generators\TokenParsers\PhpTagsParser;
use Ephect\Components\Generators\TokenParsers\UseEffectParser;
use Ephect\Components\Generators\TokenParsers\UsesAsParser;
use Ephect\Components\Generators\TokenParsers\UsesParser;
use Ephect\Components\Generators\TokenParsers\UseVariablesParser;
use Ephect\Components\Generators\TokenParsers\ValuesParser;
use Ephect\IO\Utils;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;

class ParserService
{
    protected $component = null;
    protected $useVariables = [];
    protected $html = '';
    protected $children = null;
    protected $componentList = [];
    protected $openComponentList = [];
    protected $result = null;

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

    public function doBlocks(FileComponentInterface $component): void
    {
        $p = new BlocksParser($component);
        $p->do();

        $this->result = $p->getResult();
    }

    public function doUses(FileComponentInterface $component): void
    {
        $p = new UsesParser($component);
        $p->do();
    }

    public function doUsesAs(FileComponentInterface $component): void
    {
        $p = new UsesAsParser($component);
        $p->do();
    }

    public function doPhpTags(FileComponentinterface $component): void
    {
        $p = new PhpTagsParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doChildrenDeclaration(FileComponentinterface $component): void
    {
        $p = new ChildrenDeclarationParser($component);
        $p->do();
        $this->children = (object) $p->getResult();
    }

    public function doValues(FileComponentinterface $component): void
    {
        $p = new ValuesParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doEchoes(FileComponentinterface $component): void
    {
        $p = new EchoParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doArrays(FileComponentinterface $component): void
    {
        $p = new ArraysParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doUseEffect(FileComponentinterface $component): void
    {
        $p = new UseEffectParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doUseVariables(FileComponentinterface $component): void
    {
        $p = new UseVariablesParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();
    }

    public function doNamespace(FileComponentinterface $component): void
    {
        $p = new NamespaceParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doFragments(FileComponentinterface $component): void
    {
        $p = new FragmentsParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doClosedComponents(FileComponentinterface $component): void
    {
        $p = new ClosedComponentsParser($component);
        $p->do();
        $this->componentList = $p->getResult();
        $this->html = $p->getHtml();
    }

    public function doOpenComponents(FileComponentinterface $component): void
    {
        $p = new OpenComponentsParser($component);
        $p->do();
        $this->openComponentList = $p->getResult();
        $this->html = $p->getHtml();
    }

    public function updateFile(FileComponentinterface $component): void 
    {
        $cp = new ComponentParser($component);
        $struct = $cp->doDeclaration();
        $decl = $struct->toArray();
        $filename = $component->getFlattenSourceFilename();
        Utils::safeWrite(CACHE_DIR . $component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $this->html);

        CodeRegistry::write($component->getFullyQualifiedFunction(), $decl);
        CodeRegistry::cache();
    }

    public function doIncludes(FileComponentinterface $component): void
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
