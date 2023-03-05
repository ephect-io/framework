<?php

namespace Ephect\Framework\Core;

class Enumerator implements EnumeratorInterface
{
    protected int $value = 0;
    protected static Enumerator|null $instance = null;

    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function enum(?int $value = null): ?int
    {
        if ($value !== null) {
            static::$instance = new Enumerator($value);
            return null;
        }
        return static::$instance->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
