<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class PhpTagsCleaner extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $phtml = $parameter;

        $re = '/\?>( |\s+?)<\?php/m';
        $phtml = preg_replace($re, "$1", $phtml);

        $re = '/<\?=(\$\w+)\ +\n/m';
        $phtml = preg_replace($re, "<?php echo $1;\n", $phtml);

        $this->result = $phtml;
    }
}
