<?php

namespace Ephect\Framework\CLI;

use Ephect\Framework\CLI\Enums\ConsoleColorsEnum;
use Ephect\Framework\Utils\Text;

class ConsoleColors
{
    public static function getColoredString(string|array $string, ConsoleColorsEnum|null $foreground_color = null, ConsoleColorsEnum|null $background_color = null): string
    {
        if (is_array($string)) {
            $string = Text::arrayToString($string, true);
        }

        $colored_string = "";
        $suffix = "\033[0m";

        // Check if given foreground color found
        if (isset($foreground_color)) {
            $colored_string .= "\033[" . $foreground_color->value() . "m";
        }
        // Check if given background color found
        if (isset($background_color)) {
            $colored_string = "\033[" . $background_color->value() . "m" . $colored_string;
            $suffix .= $suffix;
        }

        // Add string and end coloring
        $colored_string .= $string . $suffix;

        return $colored_string;
    }
}
