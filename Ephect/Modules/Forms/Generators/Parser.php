<?php

namespace Ephect\Modules\Forms\Generators;

use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\Forms\Registry\CodeRegistry;

class Parser implements ParserInterface
{
    protected ?string $html = '';
    protected ?ComponentInterface $component = null;

    public function __construct(string|ComponentInterface $comp, protected string $buildDirectory = \Constants::CACHE_DIR)
    {
        if (is_string($comp)) {
            $this->component = null;
            $this->html = $comp;
        } else {
            $this->component = $comp;
            $this->html = $comp->getCode();
            $this->doUncache();
        }
    }

    public function doUncache(): bool
    {
        CodeRegistry::setCacheDirectory($this->buildDirectory . $this->component->getMotherUID());
        return CodeRegistry::load();
    }

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $result .= '"' . $key . '" => "' . urlencode($value) . '", ';
        }
        return ($result === '') ? null : '[' . $result . ']';
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function doCache(): bool
    {
        return CodeRegistry::save();
    }

    public function doArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_-]+)(\[])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) }}|\{([\S ]*)})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[3], 1), 0, -1);

            if (isset($match[2]) && $match[2] === '[]') {
                if (!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getBuildDirectory(): string
    {
        return $this->buildDirectory;
    }
}
