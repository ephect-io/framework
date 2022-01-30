<?php

namespace Ephect\Hooks;

function useQueryArgument($arg, $default = ''): string
{
    $result = '';

    // mysql_escape_string
    if (isset($_POST[$arg])) {
        $result = filter_input(INPUT_POST, $arg, FILTER_SANITIZE_STRING);
        if (is_array($_POST[$arg])) {
            $result = filter_input(INPUT_POST, $arg, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        }
        return $result;
    }

    if (isset($_GET[$arg])) {
        $result = filter_input(INPUT_GET, $arg, FILTER_SANITIZE_STRING);
        return $result;
    }

    if ($result === '') {
        $result = $default;
    }
    return $result;
}