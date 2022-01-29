<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class FragmentsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $this->html = str_replace('<>', '', $this->html);
        $this->html = str_replace('</>', '', $this->html);

    }
    
}