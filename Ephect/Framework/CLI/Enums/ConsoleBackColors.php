<?php

namespace Ephect\Framework\CLI\Enums;

enum ConsoleBackColors
{
    case BACKGROUND_BLACK;
    case BACKGROUND_RED;
    case BACKGROUND_GREEN;
    case BACKGROUND_YELLOW;
    case BACKGROUND_BLUE;
    case BACKGROUND_MAGENTA;
    case BACKGROUND_CYAN;
    case BACKGROUND_LIGHT_GRAY;

    public function value(): string
    {
        return match ($this) {
            self::BACKGROUND_BLACK => '40',
            self::BACKGROUND_RED => '41',
            self::BACKGROUND_GREEN => '42',
            self::BACKGROUND_YELLOW => '43',
            self::BACKGROUND_BLUE => '44',
            self::BACKGROUND_MAGENTA => '45',
            self::BACKGROUND_CYAN => '46',
            self::BACKGROUND_LIGHT_GRAY => '47',
        };
    }


}
