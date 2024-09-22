<?php

namespace Forms\Generators;

use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Forms\Generators\TokenParsers\ArraysParser;
use Forms\Generators\TokenParsers\AttributesParser;
use Forms\Generators\TokenParsers\ChildrenDeclarationParser;
use Forms\Generators\TokenParsers\ChildSlotsParser;
use Forms\Generators\TokenParsers\ClosedComponentsParser;
use Forms\Generators\TokenParsers\EmptyComponentsParser;
use Forms\Generators\TokenParsers\FragmentsParser;
use Forms\Generators\TokenParsers\HeredocParser;
use Forms\Generators\TokenParsers\HtmlParser;
use Forms\Generators\TokenParsers\ModuleComponentParser;
use Forms\Generators\TokenParsers\NamespaceParser;
use Forms\Generators\TokenParsers\OpenComponentsParser;
use Forms\Generators\TokenParsers\ReturnTypeParser;
use Forms\Generators\TokenParsers\UseEffectParser;
use Forms\Generators\TokenParsers\UsesAsParser;
use Forms\Generators\TokenParsers\UsesParser;
use Forms\Generators\TokenParsers\UseVariablesParser;
use Forms\Generators\TokenParsers\View\InlineCodeParser;

class ParserService implements ParserServiceInterface
{
    protected ?object $component = null;
    protected array $funcVariables = [];
    protected array $useVariables = [];
    protected array $attributes = [];
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

    public function getUses(): ?array
    {
        return $this->useTypes;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
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

    public function doReturnType(FileComponentInterface $component): void
    {
        $p = new ReturnTypeParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doAttributes(FileComponentInterface $component): void
    {
        $p = new AttributesParser($component);
        $p->do();
        $this->attributes = $p->getResult();
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
