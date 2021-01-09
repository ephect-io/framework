<?php

function useEffect($callback, ...$params)
{
    call_user_func($callback, ...$params);
}

function myfunc()
{
    $a = 'Something to say.';
    useEffect(function ()  use(&$a) {
        $a = $a . '.. and something again.';
    });

    return function() use ($a) {
        echo $a;
    };
}

$fn = myfunc();
$fn();
