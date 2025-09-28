<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class ReturnTypeParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
//        $re = '/((function) ([\w]+)(\([\w\W]*\))(:( +)?string))/';
        $re = '/namespace *?([\w\\\\]+);([\w\W\\\\]*)function *?([$\w]+)\(([\w\W]*)\)(\W*):? *?(\w+)?(\W*)(\{)/U';

//        $subst = "$2 $3$4: \\Closure";
        $subst = "namespace $1;$2function $3($4): \\Closure$7$8";

        $this->html = preg_replace($re, $subst, $this->html, 1);
    }
}
