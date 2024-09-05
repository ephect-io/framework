<?php

namespace Ephect\Forms\Components\Generators;

use Ephect\Forms\Components\FileComponentInterface;
use Ephect\Forms\Components\Generators\TokenParsers\ArraysParser;
use Ephect\Forms\Components\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Forms\Components\Generators\TokenParsers\ChildSlotsParser;
use Ephect\Forms\Components\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Forms\Components\Generators\TokenParsers\EmptyComponentsParser;
use Ephect\Forms\Components\Generators\TokenParsers\FragmentsParser;
use Ephect\Forms\Components\Generators\TokenParsers\HeredocParser;
use Ephect\Forms\Components\Generators\TokenParsers\HtmlParser;
use Ephect\Forms\Components\Generators\TokenParsers\ModuleComponentParser;
use Ephect\Forms\Components\Generators\TokenParsers\NamespaceParser;
use Ephect\Forms\Components\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Forms\Components\Generators\TokenParsers\ReturnTypeParser;
use Ephect\Forms\Components\Generators\TokenParsers\UseEffectParser;
use Ephect\Forms\Components\Generators\TokenParsers\UsesAsParser;
use Ephect\Forms\Components\Generators\TokenParsers\UsesParser;
use Ephect\Forms\Components\Generators\TokenParsers\UseVariablesParser;
use Ephect\Forms\Components\Generators\TokenParsers\View\InlineCodeParser;
use Ephect\Forms\Registry\ComponentRegistry;

class ParserService implements ParserServiceInterface
{
    protected ?object $component = null;
    protected array $funcVariables = [];
    protected array $useVariables = [];
    protected array $useTypes = [];
    protected string $html = '';
    protected ?object $children = null;
    protected array $componentList = [];
    protected array $openComponentList = [];
    protected string|array|bool|null $result = null;

    public function getChildren(): ?object
    {
        return $this->children;
    }

    public function doChildSlots(FileComponentInterface $component): void
    {
        $p = new ChildSlotsParser($component);
        $p->do();
        $this->html = $p->getHtml();
        $this->result = $p->getResult();
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getResult(): null|string|array|bool
    {
        return $this->result;
    }

    public function doUses(FileComponentInterface $component): void
    {
        $p = new UsesParser($component);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function getUses(): ?array
    {
        return $this->useTypes;
    }

    public function doUsesAs(FileComponentInterface $component): void
    {
        $p = new UsesAsParser($component);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function doReturnType(FileComponentInterface $component): void
    {
        $p = new ReturnTypeParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doHeredoc(FileComponentInterface $component): void
    {
        $p = new HeredocParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doInlineCode(FileComponentInterface $component): void
    {
        $this->doHtml($component);
        $text = $this->result;

        $p = new InlineCodeParser($component);
        $p->do([
            "html" => $text,
            "useVariables" => $this->useVariables,
        ]);
        $phtml = $p->getResult();

        $this->html = str_replace($text, $phtml, $this->html);

        $this->useVariables = $p->getUseVariables();
        $this->funcVariables = $p->getFuncVariables();
    }

    public function doHtml(FileComponentInterface $component): void
    {
        $p = new HtmlParser($component);
        $p->do();
        $this->result = $p->getResult();
    }

    public function getUseVariables(): ?array
    {
        return $this->useVariables;
    }

    public function getFuncVariables(): ?array
    {
        return $this->funcVariables;
    }

    public function doChildrenDeclaration(FileComponentInterface $component): void
    {
        $p = new ChildrenDeclarationParser($component);
        $p->do();
        $this->children = (object)$p->getResult();
    }

    public function doArrays(FileComponentInterface $component): void
    {
        $p = new ArraysParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
        $this->html = $p->getHtml();
    }

    public function doUseEffect(FileComponentInterface $component): void
    {
        $p = new UseEffectParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doUseVariables(FileComponentInterface $component): void
    {
        $p = new UseVariablesParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
        $this->html = $p->getHtml();
    }

    public function doModuleComponent(FileComponentInterface $component): void
    {
        $p = new ModuleComponentParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
    }

    public function doNamespace(FileComponentInterface $component): void
    {
        $p = new NamespaceParser($component);
        $p->do($component->getMotherUID());
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

    public function doEmptyComponents(FileComponentInterface $component): void
    {
        $p = new EmptyComponentsParser($component);
        $p->do();
        $this->html = $p->getHtml();
        $this->result = $p->getResult();
    }

    public function doIncludes(FileComponentInterface $component): void
    {
        $componentList = array_unique(array_merge($this->componentList, $this->openComponentList));

        ComponentRegistry::load();
        $motherUID = $component->getMotherUID();

        foreach ($componentList as $componentName) {
            [$fqFunctionName, $cacheFilename] = $component->renderComponent($motherUID, $componentName);

            $include = str_replace('%s', $cacheFilename, INCLUDE_PLACEHOLDER);

            $re = '/(namespace +[\w\\\\]+;)/m';
            preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

            if (!isset($matches[0])) {
                $re = '/(<\?php)/m';
            }

            $subst = '$1' . PHP_EOL . '<Include />';
            $this->html = preg_replace($re, $subst, $this->html, 1);

            $this->html = str_replace('<Include />', $include, $this->html);
        }
    }
}
