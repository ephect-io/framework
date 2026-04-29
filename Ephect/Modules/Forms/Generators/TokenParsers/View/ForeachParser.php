<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class ForeachParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        if(!str_starts_with(trim($parameter), '@for')) {
            $this->result = $parameter;
            return;
        }

        $re = '/@for +((%[\w\->()\[\]]+) +as +(%\w+( +=> +%\w+)?)) +do/m';
        $subst = "<% foreach($2 as $3) { %>";
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }
}
