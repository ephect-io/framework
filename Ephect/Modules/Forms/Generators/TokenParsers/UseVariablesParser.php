<?php

namespace Forms\Generators\TokenParsers;

final class UseVariablesParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->useVariables = $parameter;

        $useVars = array_values($this->useVariables);
        $use = count($useVars) > 0 ? 'use(' . implode(', ', $useVars) . ') ' : '';

        $this->html = str_replace('(<<< HTML', 'function () ' . $use . '{?>', $this->html);
        $this->html = str_replace('HTML);', "<?php\n\t};", $this->html);
    }

}