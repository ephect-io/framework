<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class PhpTagsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $phtml = $parameter;

        $phtml = str_replace('<? ', '<?php ', $phtml);

        $re = '/\?\>(.|\s+)\<\?php/m';
        $phtml = preg_replace($re, "$1", $phtml);

        $this->result = $phtml;
    }
}
