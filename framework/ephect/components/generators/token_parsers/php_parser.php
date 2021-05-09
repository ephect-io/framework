<?php

namespace Ephect\Components\Generators\TokenParsers;

final class PhpTagsParser extends AbstractTokenParser
{
    public function do(): void
    {
        $this->html = str_replace('{?', '<?php ', $this->html);
        $this->html = str_replace('?}', '?> ', $this->html);
    }
}
