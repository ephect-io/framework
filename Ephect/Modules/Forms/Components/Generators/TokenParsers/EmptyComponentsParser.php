<?php

namespace Ephect\Modules\Forms\Components\Generators\TokenParsers;

class EmptyComponentsParser extends AbstractTokenParser
{

    public function do(null|string|array|object $parameter = null): void
    {
        $this->result = false;

        // To match any case, but must be tested before use to replace
        $re = '/<([A-Z]\w+)((\s[^\w\>]|.*?)*)\>((\s|.?)*?)(<\/\1>)/';

        $subject = $this->html;

        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $contents = trim($match[4]);
            if ($contents === '') {
                $this->result = true;
                $subst = '<$1$2/>';
                $tag = $match[1];
                $re = <<< REGEX
                /<({$tag})((\s[^\w\>]|.*?)*)\>((\s|.?)*?)(<\/{$tag}>)/
                REGEX;

                // Replace only the first occurrence of $tag
                $this->html = preg_replace($re, $subst, $subject, 1);
            }

        }

    }
}
