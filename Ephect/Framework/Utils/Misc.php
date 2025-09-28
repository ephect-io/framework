<?php

namespace Ephect\Framework\Utils;

use DateTimeImmutable;
use Random\Randomizer;

class Misc
{
    public static function makeId(): int
    {
        $dti = new DateTimeImmutable();
        $ts = $dti->getTimestamp();
        $string = str_split($ts);

        $r = new Randomizer();
        $result = implode('', $r->shuffleArray($string));

        return intval($result);
    }
}
