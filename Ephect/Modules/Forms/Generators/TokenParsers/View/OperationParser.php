<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class OperationParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@op +(.*)$/m';
        $subst = "<% $1; %>";
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
