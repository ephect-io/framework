<?php

namespace Ephect\Modules\Forms\Components\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Components\Generators\TokenParsers\AbstractTokenParser;

final class ElseParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@else *$/m';
        $subst = '<%} else {%>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
