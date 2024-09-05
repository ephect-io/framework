<?php

namespace Ephect\Forms\Components\Generators\TokenParsers;

use Ephect\Forms\Components\FileComponentInterface;
use Ephect\Forms\Components\Generators\ParserServiceInterface;
use Ephect\Forms\Registry\CodeRegistry;

abstract class AbstractTokenParser implements TokenParserInterface
{

    protected ?string $html = '';
    protected ?FileComponentInterface $component = null;
    protected string|array|bool|null $result = null;
    protected array $funcVariables = [];
    protected array $useVariables = [];
    protected array $useTypes = [];
    protected ?ParserServiceInterface $parent = null;

    public function __construct(FileComponentInterface $comp, ?ParserServiceInterface $parent = null)
    {
        $this->parent = $parent;
        $this->component = $comp;
        $this->html = $comp->getCode();
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getResult(): null|string|array|bool
    {
        return $this->result;
    }

    public function getFuncVariables(): ?array
    {
        return $this->funcVariables;
    }

    public function getUseVariables(): ?array
    {
        return $this->useVariables;
    }

    public function getUses(): ?array
    {
        return $this->useTypes;
    }

    public function doCache(): bool
    {
        return CodeRegistry::save();
    }

    public function doUncache(): bool
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->component->getMotherUID());
        return CodeRegistry::load();
    }

    abstract public function do(null|string|array|object $parameter = null): void;
}