<?php

namespace FunCom\Plugins;

function Router($children)
{
    return (<<<HTML
        {{ children }}
    HTML);
}