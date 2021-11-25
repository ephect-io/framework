<?php

namespace Ephect\CLI;

class ConsoleColors
{

    // Set up shell colors
    const BLACK = '0;30';
    const DARK_GRAY = '1;30';
    const BLUE = '0;34';
    const LIGHT_BLUE = '1;34';
    const GREEN = '0;32';
    const LIGHT_GREEN = '1;32';
    const CYAN = '0;36';
    const LIGHT_CYAN = '1;36';
    const RED = '0;31';
    const LIGHT_RED = '1;31';
    const PURPLE = '0;35';
    const LIGHT_PURPLE = '1;35';
    const BROWN = '0;33';
    const YELLOW = '1;33';
    const LIGHT_GRAY = '0;37';
    const WHITE = '1;37';

    const BACKGROUND_BLACK = '40';
    const BACKGROUND_RED = '41';
    const BACKGROUND_GREEN = '42';
    const BACKGROUND_YELLOW = '43';
    const BACKGROUND_BLUE = '44';
    const BACKGROUND_MAGENTA = '45';
    const BACKGROUND_CYAN = '46';
    const BACKGROUND_LIGHT_GRAY = '47';

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
        $colored_string .=  $string . $suffix;

        return $colored_string;
    }
}
