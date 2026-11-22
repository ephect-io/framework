<?php

namespace Ephect\Framework\CLI\Enums;

enum ConsoleColors
{
    case BLACK;
    case DARK_GRAY;
    case BLUE;
    case LIGHT_BLUE;
    case GREEN;
    case LIGHT_GREEN;
    case CYAN;
    case LIGHT_CYAN;
    case RED;
    case LIGHT_RED;
    case PURPLE;
    case LIGHT_PURPLE;
    case BROWN;
    case YELLOW;
    case LIGHT_GRAY;
    case WHITE;

    public function value(): string
    {
        return match ($this) {
            self::BLACK => '0;30',
            self::DARK_GRAY => '1;30',
            self::BLUE => '0;34',
            self::LIGHT_BLUE => '1;34',
            self::GREEN => '0;32',
            self::LIGHT_GREEN => '1;32',
            self::CYAN => '0;36',
            self::LIGHT_CYAN => '1;36',
            self::RED => '0;31',
            self::LIGHT_RED => '1;31',
            self::PURPLE => '0;35',
            self::LIGHT_PURPLE => '1;35',
            self::BROWN => '0;33',
            self::YELLOW => '1;33',
            self::LIGHT_GRAY => '0;37',
            self::WHITE => '1;37',
        };
    }


}
