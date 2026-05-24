<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class EchoParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        if(!str_starts_with(trim($parameter), '@echo')) {
            $this->result = $parameter;
            return;
        }

        $re = '/@echo +(.+)$/m';
        $subst = '<% echo $1; %>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }
}
