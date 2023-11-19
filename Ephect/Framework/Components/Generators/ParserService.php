<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\Components\FileComponentInterface;
use Ephect\Framework\Components\Generators\TokenParsers\ArgumentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\ArraysParser;
use Ephect\Framework\Components\Generators\TokenParsers\ChildrenDeclarationParser;
use Ephect\Framework\Components\Generators\TokenParsers\ChildSlotsParser;
use Ephect\Framework\Components\Generators\TokenParsers\ClosedComponentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\EmptyComponentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\FragmentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\HeredocParser;
use Ephect\Framework\Components\Generators\TokenParsers\HtmlParser;
use Ephect\Framework\Components\Generators\TokenParsers\NamespaceParser;
use Ephect\Framework\Components\Generators\TokenParsers\OpenComponentsParser;
use Ephect\Framework\Components\Generators\TokenParsers\PhpTagsCleaner;
use Ephect\Framework\Components\Generators\TokenParsers\UseEffectParser;
use Ephect\Framework\Components\Generators\TokenParsers\UsesAsParser;
use Ephect\Framework\Components\Generators\TokenParsers\UsesParser;
use Ephect\Framework\Components\Generators\TokenParsers\UseVariablesParser;
use Ephect\Framework\Components\Generators\TokenParsers\View\InlineCodeParser;
use Ephect\Framework\Components\Generators\TokenParsers\WebComponentParser;
use Ephect\Framework\Registry\ComponentRegistry;

class ParserService implements ParserServiceInterface
{
    protected ?object $component = null;
    protected array $useVariables = [];
    protected array $useTypes = [];
    protected string $html = '';
    protected ?object $children = null;
    protected $componentList = [];
    protected $openComponentList = [];
    protected $result = null;

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

    public function getResult(): null|string|array|bool
    {
        return $this->result;
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

    public function doHeredoc(FileComponentInterface $component): void
    {
        $p = new HeredocParser($component);
        $p->do();
        $this->html = $p->getHtml();
    }

    public function doHtml(FileComponentInterface $component): void
    {
        $p = new HtmlParser($component);
        $p->do();
        $this->result = $p->getResult();
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

        $this->html = str_replace($text,  $phtml, $this->html);

        $this->useVariables = $p->getVariables();
    }

    public function doChildrenDeclaration(FileComponentInterface $component): void
    {
        $p = new ChildrenDeclarationParser($component);
        $p->do();
        $this->children = (object)$p->getResult();
    }

    public function getVariables(): ?array
    {
        return $this->useVariables;
    }

    public function doArrays(FileComponentInterface $component): void
    {
        $p = new ArraysParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
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

    public function doWebComponent(FileComponentInterface $component): void
    {
        $p = new WebComponentParser($component);
        $p->do($this->useVariables);
        $this->useVariables = $p->getVariables();
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

        ComponentRegistry::uncache();
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
