<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

final class FunctionParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $subject = $this->html;
        $re = '/namespace *?([\w\\\\]+);([\w\W\\\\]*)function *?([$\w]+)\(([\w\W]*)\)(\W*):? *?(\w+)?(\W*)(\{)/U';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        $this->result = !isset($matches[0]) ? '' : $matches[0][1];
    }
}
