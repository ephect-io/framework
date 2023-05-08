<?php

namespace Ephect\Framework\Core;

interface EnumeratorInterface
{
    public static function enum(?int $value = null): ?int;

    public function getValue();
}
