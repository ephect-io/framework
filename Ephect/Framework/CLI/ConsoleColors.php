<?php

namespace Ephect\Framework\CLI;

class ConsoleColors
{
    // Set up shell colors
    public const BLACK = '0;30';
    public const DARK_GRAY = '1;30';
    public const BLUE = '0;34';
    public const LIGHT_BLUE = '1;34';
    public const GREEN = '0;32';
    public const LIGHT_GREEN = '1;32';
    public const CYAN = '0;36';
    public const LIGHT_CYAN = '1;36';
    public const RED = '0;31';
    public const LIGHT_RED = '1;31';
    public const PURPLE = '0;35';
    public const LIGHT_PURPLE = '1;35';
    public const BROWN = '0;33';
    public const YELLOW = '1;33';
    public const LIGHT_GRAY = '0;37';
    public const WHITE = '1;37';

    public const BACKGROUND_BLACK = '40';
    public const BACKGROUND_RED = '41';
    public const BACKGROUND_GREEN = '42';
    public const BACKGROUND_YELLOW = '43';
    public const BACKGROUND_BLUE = '44';
    public const BACKGROUND_MAGENTA = '45';
    public const BACKGROUND_CYAN = '46';
    public const BACKGROUND_LIGHT_GRAY = '47';

    // Returns coloRED string
    public static function getColoredString($string, $foreground_color = null, $background_color = null): string
    {
        $colored_string = "";
        $suffix = "\033[0m";

        // Check if given foreground color found
        if (isset($foreground_color)) {
            $colored_string .= "\033[" . $foreground_color . "m";
        }
        // Check if given background color found
        if (isset($background_color)) {
            $colored_string = "\033[" . $background_color . "m" . $colored_string;
            $suffix .= $suffix;
        }

        // Add string and end coloring
        $colored_string .= $string . $suffix;

        return $colored_string;
    }
}
