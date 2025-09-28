<?php

namespace Ephect\Hooks;

function useRouteParams($base64Params)
{
    $params = base64_decode($base64Params);
    return json_decode($params);
}
