<?php

namespace Ephect\Components\Generators;

use Ephect\Components\FileComponentInterface;
use Ephect\Components\Generators\TokenParsers\ArraysParser;
use Ephect\Components\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Components\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Components\Generators\TokenParsers\EchoParser;
use Ephect\Components\Generators\TokenParsers\FragmentsParser;
use Ephect\Components\Generators\TokenParsers\NamespaceParser;
use Ephect\Components\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Components\Generators\TokenParsers\PhpTagsParser;
use Ephect\Components\Generators\TokenParsers\UseEffectParser;
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

    public function __construct(FileComponentInterface $comp)
    {
        $this->component = $comp;
        $this->html = $comp->getCode();
    }

    public function parse(): void
    {
        $p = new PhpTagsParser($this->component);
        $p->do();

        $p = new ChildrenDeclarationParser($this->component);
        $p->do();
        $children = (object) $p->getResult();

        $p = new ValuesParser($this->component);
        $p->do();
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();

        $p = new EchoParser($this->component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();

        $p = new ArraysParser($this->component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();

        $p = new UseEffectParser($this->component);
        $p->do();
        $this->html = $p->getHtml();

        $p = new UseVariablesParser($this->component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
        $this->html = $p->getHtml();

        $p = new NamespaceParser($this->component);
        $p->do();
        $this->html = $p->getHtml();

        $p = new FragmentsParser($this->component);
        $p->do();
        $this->html = $p->getHtml();

        $p = new ClosedComponentsParser($this->component);
        $p->do();
        $componentList = $p->getResult();
        $this->html = $p->getHtml();
        $this->updateFile();

        $p = new OpenComponentsParser($this->component);
        $p->do();
        $openComponentList = $p->getResult();
        $this->html = $p->getHtml();
        $this->updateFile();

        $this->componentList = array_unique(array_merge($componentList, $openComponentList));

        $this->doIncludes();
    }

    public function updateFile():  void 
    {
        $cp = new ComponentParser($this->component);
        $struct = $cp->doDeclaration();
        $decl = $struct->toArray();
        $filename = $this->component->getFlattenSourceFilename();
        Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $this->html);

        CodeRegistry::write($this->component->getFullyQualifiedFunction(), $decl);
        CodeRegistry::cache();
    }

    public function doIncludes(): void
    {
        ComponentRegistry::uncache();
        $motherUID = $this->component->getMotherUID();

        foreach ($this->componentList as $component) {
            [$fqFunctionName, $cacheFilename] = $this->component->renderComponent($motherUID, $component);

            $moduleNs = "namespace " . $this->component->getNamespace() . ';' . PHP_EOL;
            $include = str_replace('%s', $cacheFilename, INCLUDE_PLACEHOLDER);
            $this->code = str_replace($moduleNs, $moduleNs . PHP_EOL . $include, $this->code);

        }
    }
}
