<?php

namespace Ephect\Modules\DataAccess\Hooks;

use Ephect\Framework\Utils\File;
use Ephect\Modules\DataAccess\Configuration\ConnectionConfiguration;
use Ephect\Modules\DataAccess\Configuration\ConnectionStructure;
use InvalidArgumentException;

function useConfig(string $name): ConnectionConfiguration
{
    $congfigurationFile = \Constants::APP_DATA . $name . \Constants::JSON_EXTENSION;

    if (!file_exists($congfigurationFile)) {
        throw new InvalidArgumentException('Configuration file not found.');
    }

    $json = File::safeRead($congfigurationFile);

    $result = null;
    try {
        $structure = new ConnectionStructure();
        $structure->decode($json);
        $result = new ConnectionConfiguration($structure);
    } catch (InvalidArgumentException $e) {
        throw new InvalidArgumentException('Invalid configuration structure.');
    }

    // TODO: validate JSON with Structure.
    return $result;
}
