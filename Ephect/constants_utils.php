<?php

function siteRoot(): string
{
    $siteRoot = DONT_USE_SITE_ROOT;
    $vendorPos = strpos(DONT_USE_SITE_ROOT, 'vendor');

    if ($vendorPos > -1) {
        $siteRoot = substr(DONT_USE_SITE_ROOT, 0, $vendorPos);
    }

    return $siteRoot;
}
function siteConfigPath(): string
{
    return siteRoot() . DONT_USE_REL_CONFIG_DIR;
}

function siteRuntimePath(): string
{
    return siteRoot() . \Constants::REL_RUNTIME_DIR;
}

function siteSrcPath(): string
{
    $configDir = siteConfigPath();
    $srcDir = file_exists($configDir . DONT_USE_REL_CONFIG_APP) ? trim(file_get_contents($configDir . DONT_USE_REL_CONFIG_APP)) : DONT_USE_REL_CONFIG_APP;
    return siteRoot() . $srcDir . DIRECTORY_SEPARATOR;
}
