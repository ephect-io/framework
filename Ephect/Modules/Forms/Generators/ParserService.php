<?php

namespace Ephect\Modules\Forms\Generators;

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Modules\Forms\Application\ApplicationComponent;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Generators\TokenParsers\ArraysParser;
use Ephect\Modules\Forms\Generators\TokenParsers\AttributesParser;
use Ephect\Modules\Forms\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Modules\Forms\Generators\TokenParsers\ChildSlotsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\EmptyComponentsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\FragmentsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\HeredocParser;
use Ephect\Modules\Forms\Generators\TokenParsers\HtmlParser;
use Ephect\Modules\Forms\Generators\TokenParsers\ModuleComponentParser;
use Ephect\Modules\Forms\Generators\TokenParsers\NamespaceParser;
use Ephect\Modules\Forms\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\ReturnTypeParser;
use Ephect\Modules\Forms\Generators\TokenParsers\UseEffectParser;
use Ephect\Modules\Forms\Generators\TokenParsers\UsesAsParser;
use Ephect\Modules\Forms\Generators\TokenParsers\UsesParser;
use Ephect\Modules\Forms\Generators\TokenParsers\UseVariablesParser;
use Ephect\Modules\Forms\Generators\TokenParsers\View\InlineCodeParser;

use function Ephect\Hooks\useMemory;

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

    public function __construct(protected string $buildDirectory = \Constants::BUILD_DIR)
    {
    }

    public function getChildren(): ?object
    {
        return $this->children;
    }

    public function doChildSlots(FileComponentInterface $component): void
    {
        $p = new ChildSlotsParser($component, $this->buildDirectory);
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
        $p = new UsesParser($component, $this->buildDirectory);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function doUsesAs(FileComponentInterface $component): void
    {
        $p = new UsesAsParser($component, $this->buildDirectory);
        $p->do();
        $this->useTypes = array_merge($this->useTypes, $p->getUses());
    }

    public function doReturnType(FileComponentInterface $component): void
    {
        $p = new ReturnTypeParser($component, $this->buildDirectory);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doAttributes(FileComponentInterface $component): void
    {
        $p = new AttributesParser($component, $this->buildDirectory);
        $p->do();
        $this->attributes = $p->getResult();
    }

    public function doHeredoc(FileComponentInterface $component): void
    {
        $p = new HeredocParser($component, $this->buildDirectory);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doInlineCode(FileComponentInterface $component): void
    {
        $this->doHtml($component);
        $text = $this->result;

        $p = new InlineCodeParser($component, $this->buildDirectory);
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
        $p = new HtmlParser($component, $this->buildDirectory);
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
        $p = new ChildrenDeclarationParser($component, $this->buildDirectory);
        $p->do();
        $this->children = (object)$p->getResult();
    }

    public function doArrays(FileComponentInterface $component): void
    {
        $p = new ArraysParser($component, $this->buildDirectory);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
        $this->html = $p->getHtml();
    }

    public function doUseEffect(FileComponentInterface $component): void
    {
        $p = new UseEffectParser($component, $this->buildDirectory);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doUseVariables(FileComponentInterface $component): void
    {
        $p = new UseVariablesParser($component, $this->buildDirectory);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
        $this->html = $p->getHtml();
    }

    public function doModuleComponent(FileComponentInterface $component): void
    {
        $p = new ModuleComponentParser($component, $this->buildDirectory);
        $p->do($this->useVariables);
        $this->useVariables = $p->getUseVariables();
    }

    public function doNamespace(FileComponentInterface $component): void
    {
        $p = new NamespaceParser($component, $this->buildDirectory);
        $p->do($component->getMotherUID());
        $this->html = $p->getHtml();
    }

    public function doFragments(FileComponentInterface $component): void
    {
        $p = new FragmentsParser($component, $this->buildDirectory);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doClosedComponents(FileComponentInterface $component): void
    {
        $p = new ClosedComponentsParser($component, $this->buildDirectory);
        $p->do();
        $this->componentList = $p->getResult();
        $this->html = $p->getHtml();
    }

    public function doOpenComponents(FileComponentInterface $component): void
    {
        $p = new OpenComponentsParser($component, $this->buildDirectory);
        $p->do($this->useVariables);
        $this->openComponentList = $p->getResult();
        $this->html = $p->getHtml();
    }

    public function doEmptyComponents(FileComponentInterface $component): void
    {
        $p = new EmptyComponentsParser($component, $this->buildDirectory);
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
            [$fqFunction, $cacheFilename] = $component->renderComponent($motherUID, $componentName);

//            FrameworkRegistry::load();
//            FrameworkRegistry::write($fqFunction, \Constants::BUILD_DIR . $cacheFilename);
//            FrameworkRegistry::save(true);

//            $use = sprintf("use function %s;", $fqFunction);
            $include = sprintf("include_once '%s%s';", $this->buildDirectory, $cacheFilename);

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
