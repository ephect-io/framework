<?php

namespace Forms\Generators\TokenParsers\View;

use Forms\Generators\TokenParsers\AbstractTokenParser;

final class PhpTagsParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $phtml = $parameter;

        $re = '/(<%)(( +|\s+)?)/m';
        $phtml = preg_replace($re, '<?php' . "$2", $phtml);

        $re = '/%>/m';
        $phtml = preg_replace($re, "?>", $phtml);

        $this->result = $phtml;
    }
}
