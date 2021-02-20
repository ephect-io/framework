<?php

namespace Ephect\Core;

interface EnumeratorInterface
{
    public static function enum(?int $value = null): ?int;
    public function getValue();
}
