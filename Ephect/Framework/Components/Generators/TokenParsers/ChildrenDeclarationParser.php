<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class ChildrenDeclarationParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $subject = $this->html;

        $re = '/(function([\w ]+)\(\$([\w]+)[^\)]*\)(\s|.)+?(\{))(\s|.)+?(\{\{ \3 \}\})/';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $functionDeclaration = $match[1];
            $componentName = $match[2];
            $variable = $match[7];

            $this->result = ['declaration' => $functionDeclaration, 'component' => $componentName, 'variable' => $variable];
        }

    }

}