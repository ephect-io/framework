<?php

function siteRoot(): string
{
    $siteRoot = SITE_ROOT;
    $vendorPos = strpos(SITE_ROOT, 'vendor');

    if ($vendorPos > -1) {
        $siteRoot = substr(SITE_ROOT, 0, $vendorPos);
    }

    return $siteRoot;
}
function siteConfigPath(): string
{
    return siteRoot() . REL_CONFIG_DIR;
}

function siteRuntimePath(): string
{
    return siteRoot() . \Constants::REL_RUNTIME_DIR;
}

function siteSrcPath(): string
{
    $configDir = siteConfigPath();
    $srcDir = file_exists($configDir . REL_CONFIG_APP) ? trim(file_get_contents($configDir . REL_CONFIG_APP)) : REL_CONFIG_APP;
    return siteRoot() . $srcDir . DIRECTORY_SEPARATOR;
}