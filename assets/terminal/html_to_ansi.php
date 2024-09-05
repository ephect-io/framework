<?php

namespace Ephect\Assets\Terminal;

use DOMDocument;

/**
 * HTML to ANSI Converter
 *
 * Converts HTML with colored block characters into terminal-colored text
 * using ANSI color codes. Specifically designed for HTML that uses <b> tags
 * with inline style='color:#RRGGBB' attributes.
 */
class HtmlToAnsi
{
    // ANSI color constants
    public const string RESET = "\033[0m";
    public const string ESC = "\033";  // Escape character for ANSI sequences

    /**
     * Convert hex color code to ANSI true color escape sequence.
     *
     * @param string $hexColor Hex color code (with or without leading #)
     * @return string ANSI escape sequence
     */
    public function hexToAnsiTrueColor(string $hexColor): string
    {
        // Remove the '#' if present
        $hexColor = ltrim($hexColor, '#');

        // Convert hex to RGB
        $red = hexdec(substr($hexColor, 0, 2));
        $green  = hexdec(substr($hexColor, 2, 2));
        $blue  = hexdec(substr($hexColor, 4, 2));

        // Return ANSI escape sequence for true color
        return self::ESC . "[38;2;{$red};{$green};{$blue}m";
    }

    /**
     * Convert hex color code to the closest ANSI 256-color code.
     *
     * @param string $hexColor Hex color code (with or without leading #)
     * @return string ANSI escape sequence
     */
    public function hexToAnsi256(string $hexColor): string
    {
        // Remove the '#' if present
        $hexColor = ltrim($hexColor, '#');

        // Convert hex to RGB
        $red = hexdec(substr($hexColor, 0, 2));
        $green  = hexdec(substr($hexColor, 2, 2));
        $blue  = hexdec(substr($hexColor, 4, 2));

        // Calculate the closest 6x6x6 cube index in the 256-color palette
        $redIndex = intval(($red * 6) / 256);
        $greenIndex = intval(($green  * 6) / 256);
        $bIndex = intval(($blue  * 6) / 256);

        // Calculate the 256-color palette index
        $colorIndex = 16 + (36 * $redIndex) + (6 * $greenIndex) + $bIndex;

        return self::ESC . "[38;5;{$colorIndex}m";
    }

    /**
     * Convert HTML with colored blocks to ANSI colored text.
     *
     * @param string $htmlContent HTML content to convert
     * @param bool $useTrueColor Whether to use 24-bit true color
     * @return string ANSI colored text
     */
    public function convertHtmlToAnsi(string $htmlContent, bool $useTrueColor = false): string
    {
        $dom = new DOMDocument();

        // Suppress errors from malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        // Find the <pre> tag that contains our colored text
        $preTags = $dom->getElementsByTagName('pre');
        if ($preTags->length == 0) {
            return "No <pre> tag found in the HTML content.";
        }

        $pre_tag = $preTags->item(0);
        $result = [];
        $currentLine = [];

        // Process all children of the pre tag
        foreach ($pre_tag->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->tagName === 'b' && $child->hasAttribute('style')) {
                // Extract color from style attribute
                $style = $child->getAttribute('style');
                if (preg_match('/color:#([0-9A-Fa-f]{6})/', $style, $matches)) {
                    $hexColor = $matches[1];
                    $text = $child->textContent;

                    // Convert hex color to ANSI escape sequence
                    $ansi_color = $useTrueColor
                        ? $this->hexToAnsiTrueColor($hexColor)
                        : $this->hexToAnsi256($hexColor);

                    // Add colored text to current line
                    $currentLine[] = $ansi_color . $text . self::RESET;
                }
            } elseif ($child->nodeType === XML_TEXT_NODE) {
                // Handle text nodes (including newlines and regular text)
                $text = $child->textContent;

                // Check if this text node contains newlines
                if (strpos($text, "\r\n") !== false || strpos($text, "\n") !== false) {
                    // Complete current line before the newline
                    if (!empty($currentLine)) {
                        $result[] = implode('', $currentLine);
                        $currentLine = [];
                    }

                    // Process the text node by splitting it on newlines
                    $lines = preg_split('/\r\n|\n/', $text);
                    foreach ($lines as $index => $line) {
                        // Skip empty lines at the beginning/end of the text node
                        $trimmed = trim($line);
                        if ($trimmed !== '' || ($index > 0 && $index < count($lines) - 1)) {
                            $currentLine[] = $trimmed;
                            // If we have contents and, it's not the last line, add to results
                            if ($index < count($lines) - 1) {
                                $result[] = implode('', $currentLine);
                                $currentLine = [];
                            }
                        }
                    }
                } else {
                    // This is a text node without newlines, add it to current line
                    $trimmed = trim($text);
                    if ($trimmed !== '') {
                        $currentLine[] = $trimmed;
                    }
                }
            }
        }

        // Add any remaining content
        if (!empty($currentLine)) {
            $result[] = implode('', $currentLine);
        }

        return implode("\n", $result);
    }

    /**
     * Display usage information.
     */
    public function displayUsage(): void
    {
        echo "Usage: php html_to_ansi.php [--true-color] [input_file]\n";
        echo "Convert HTML with colored blocks to ANSI colored terminal text.\n\n";
        echo "Options:\n";
        echo "  --true-color    Use 24-bit true color (RGB) ANSI codes instead of 256-color codes\n";
        echo "  input_file      HTML file to convert (defaults to stdin if not provided)\n";
    }

    /**
     * Main function to process HTML input and output ANSI colored text.
     */
    protected function __run($argv): void
    {
        // Parse command line arguments
        $options = getopt("h", ["help", "true-color"]);

        // Check for help option
        if (isset($options['h']) || isset($options['help'])) {
            $this->displayUsage();
            exit(0);
        }

        // Determine if true color should be used
        $useTrueColor = isset($options['true-color']);

        // Remove the script name and parsed options from the arguments
        $non_option_args = array_values(array_filter($argv, function ($arg) {
            return $arg[0] != '-' && strpos($arg, 'html_to_ansi.php') === false;
        }));

        // Read input
        if (count($non_option_args) > 0) {
            $input_file = $non_option_args[0];
            if (!file_exists($input_file)) {
                fwrite(STDERR, "Error: File not found: $input_file\n");
                exit(1);
            }
            $htmlContent = file_get_contents($input_file);
        } else {
            // Read from stdin
            $htmlContent = file_get_contents('php://stdin');
        }

        // Convert and print
        $ansi_output = $this->convertHtmlToAnsi($htmlContent, $useTrueColor);
        echo $ansi_output;
    }

    public static function main(array $argv): void
    {
        $htmlToAnsi = new HtmlToAnsi();
        $htmlToAnsi->__run($argv);
    }
}

// Only execute main if called directly (not included)
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    HtmlToAnsi::main($argv);
}
