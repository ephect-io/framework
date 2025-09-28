<?php

namespace Ephect\Hooks;

function useImport(string $export, string $from): string
{
    return "import { $export } from $from" . PHP_EOL;
}
