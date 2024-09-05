<?php

namespace Ephect\Hooks;

function useQueryArgument($arg, $default = ''): string|array
{

    // mysql_escape_string
    if (isset($_POST[$arg])) {
        $result = htmlspecialchars(filter_input(INPUT_POST, $arg));
        if (is_array($_POST[$arg])) {
            $result = filter_input(INPUT_POST, $arg, FILTER_REQUIRE_ARRAY);
        }
        return $result;
    }

    if (isset($_GET[$arg])) {
        $result = filter_input(INPUT_GET, $arg, FILTER_SANITIZE_STRING);
        return $result;
    }

    return $default;
}