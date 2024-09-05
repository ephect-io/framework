<?php

namespace Ephect\Forms\Components\Generators\TokenParsers\View;

use Ephect\Forms\Components\Generators\TokenParsers\AbstractTokenParser;

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
