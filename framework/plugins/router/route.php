<?php

namespace FunCom\Plugins;

function Route($children)
{
    $props = ((object) $children)->props;

    return (<<<HTML
        {{ ...props }}
    HTML);
}