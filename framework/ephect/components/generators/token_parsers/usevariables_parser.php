<?php

namespace Ephect\Components\Generators\TokenParsers;

final class UseVariablesParser extends AbstractTokenParser
{
    public function do(): void
    {
        $useVars = array_values($this->useVariables);
        $use = count($useVars) > 0 ? 'use(' . implode(', ', $useVars) . ') ' : '';

        $this->html = str_replace('(<<< HTML', 'function () ' . $use . '{?>', $this->html);
        $this->html = str_replace('HTML);', "<?php\n\t};", $this->html);
    }
    
}