<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Modules\Forms\Registry\ComponentRegistry;

final class UsesAsParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $compNamespace = $this->component->getNamespace();

        $re = '/use\s+function\s+([\w\\\\]*\\\\)?(\w*)\s+as\s+(\w*);/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[1], '\\');
            $componentFunction = $match[2];
            $componentAlias = $match[3];

            $fqFunctionName = $componentNamespace . '\\' . $componentFunction;
            $this->useTypes[] = $fqFunctionName;

            ComponentRegistry::write($componentAlias, $fqFunctionName);
        }
    }
}
