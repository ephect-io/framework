<?php

$cwd = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include $cwd
    . file_get_contents($cwd . 'config' . DIRECTORY_SEPARATOR . 'framework')
    . DIRECTORY_SEPARATOR . 'bootstrap.php';
