<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\FrameworkRegistry;

final class UsesParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null) : void
    {
        $re = '/use ([A-Za-z0-9\\\\ ]*)\\\\([A-Za-z0-9]*)([ ]*)?;/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[1], '\\');
            $componentFunction = $match[2];

            $fqFunction = $componentNamespace . '\\' . $componentFunction;
            $this->useTypes[] = $fqFunction;

            $frameworkUse = FrameworkRegistry::read($fqFunction);
            if ($frameworkUse !== null) {
                continue;
            }

            ComponentRegistry::write($componentFunction, $fqFunction);
        }
    }
    
}