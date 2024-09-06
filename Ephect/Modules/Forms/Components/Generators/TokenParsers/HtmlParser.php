<?php

namespace Ephect\Modules\Forms\Components\Generators\TokenParsers;

final class HtmlParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $subject = $this->html;

        $re = '/return \(<<< ?HTML((.|\s|\R)+)HTML\);/m';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        $this->result = !isset($matches[0]) ? '' : $matches[0][1];
    }

}
