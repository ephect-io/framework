<?php

namespace Ephect\WebApp\Builder\Copiers\Strategy;

class CopiersFactory
{
    public static function createCopier(bool $asUnique = false): CopierStrategyInterface
    {
        return match ($asUnique) {
            true => new CopyAsUniqueStrategy,
            false => new CopyAsIsStrategy,
        };
    }
}