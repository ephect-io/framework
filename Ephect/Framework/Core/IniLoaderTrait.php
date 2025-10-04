<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Registry\StateRegistry;

use function pathinfo;

trait IniLoaderTrait
{
    public function loadINI(string $path = ''): bool
    {
        if (!file_exists($path . 'app.ini')) {
            return false;
        }

        $ini = parse_ini_file($path . 'app.ini', true, INI_SCANNER_TYPED);
        if (isset($ini['application']['name'])) {
            StateRegistry::writeItem('application', 'name', $ini['application']['name']);
        }
        if (isset($ini['application']['title'])) {
            StateRegistry::writeItem('application', 'title', $ini['application']['title']);
        }

        foreach ($ini as $key => $values) {
            StateRegistry::writeItem('ini', $key, $values);
        }
        unset($ini);

        $dataPath = realpath(dirname($path) . DIRECTORY_SEPARATOR . 'data');
        if (file_exists($dataPath)) {
            $dataDir = dir($dataPath);

            while (($entry = $dataDir->read()) !== false) {
                $info = (object)pathinfo($entry);

                if ($info->extension == 'json') {
                    $conf = file_get_contents($dataPath . DIRECTORY_SEPARATOR . $entry);
                    $conf = json_decode($conf, true);
                    StateRegistry::writeItem('connections', $info->filename, $conf);
                }
            }
            $dataDir->close();
        }

        return true;
    }
}
