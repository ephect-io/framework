<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\FileComponentInterface;
use Ephect\Registry\CodeRegistry;

abstract class AbstractTokenParser implements TokenParserInterface
{

    protected $html = '';
    protected $component = null;
    protected $result = null;
    protected $useVariables = [];

    public function __construct(FileComponentInterface $comp)
    {
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

    public function getVariables(): ?array
    {
        return $this->useVariables;
    }

    public function doCache(): bool
    {
        return CodeRegistry::cache();
    }

    public function doUncache(): bool
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->component->getMotherUID());
        return CodeRegistry::uncache();
    }

    abstract public function do(null|string|array $parameter = null): void;
}