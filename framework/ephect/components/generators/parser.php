<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentInterface;
use Ephect\Registry\CodeRegistry;

class Parser
{
    protected $html = '';
    protected $component = null;

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->html = $comp->getCode();
        $this->doUncache();
    }

    public function getHtml()
    {
        return $this->html;
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


    public function doArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_]*)(\[\])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/m';

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

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $result .= '"' . $key . '" => "' . urlencode($value) . '", ';
        }
        $result = ($result === '') ? null : '[' . $result . ']';

        return $result;
    }
}
