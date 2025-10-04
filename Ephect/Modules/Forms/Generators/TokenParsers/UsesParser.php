<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

final class UsesParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/use\s+(function\s+)?([\w\\\\]+)\\\\(\w+)\s*?;/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[2], '\\');
            $componentFunction = $match[3];

            $fqFunction = $componentNamespace . '\\' . $componentFunction;
            $this->useTypes[] = $fqFunction;

            if (FrameworkRegistry::read($fqFunction) !== null) {
                continue;
            }

            ComponentRegistry::write($componentFunction, $fqFunction);
        }
    }
}
