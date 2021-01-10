<?php
namespace Ephel\Core;

class Enumerator implements EnumeratorInterface
{
    protected $value = 0;
    protected static $instance = null;

    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function enum(?int $value = null): ?int
    {
        if ($value !== null) {
            static::$instance = new TEnumerator($value);
            return null;
        }
        return static::$instance->getValue();
    }

    public function getValue()
    {
        return $this->value;
    }
}
