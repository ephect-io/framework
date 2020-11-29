<?php

$re = '/\<([A-Za-z0-9]*)([ ])((\s|[^\/\>].)+)?\/\>/';
$str = '<Component1 />

<Component2 onclick={handleClick} />

<Component3 onclick={fn($e) => { 
handleClick($e);
}} />
';

preg_match_all($re, $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

// Print the entire match result
var_dump($matches);
