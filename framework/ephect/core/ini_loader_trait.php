<?php 

namespace Ephect\Core;

use Ephect\Registry\StateRegistry;

trait IniLoaderTrait
{
    public function loadINI(string $path = ''): bool
    {
        $ini = null;
        if (!file_exists($path . 'app.ini')) {
            return false;
        }

        $ini = parse_ini_file($path  . 'app.ini', TRUE, INI_SCANNER_TYPED);
        if(isset($ini['application']['name'])) {
            StateRegistry::write('application', 'name', $ini['application']['name']);
        }
        if(isset($ini['application']['title'])) {
            StateRegistry::write('application', 'title', $ini['application']['title']);
        }
        
        foreach($ini as $key=>$values) {
            StateRegistry::write('ini', $key, $values);
        }
        unset($ini);

        $dataPath = realpath($path . 'data');
        if(file_exists($dataPath)) {
            $dataDir = dir($dataPath);

            $entry = '';
            while (($entry = $dataDir->read()) !== false) {
                $info = (object) \pathinfo($entry);

                if ($info->extension == 'json') {
                    $conf = file_get_contents($dataPath . DIRECTORY_SEPARATOR . $entry);
                    $conf = json_decode($conf, true);
                    StateRegistry::write('connections', $info->filename, $conf);
                }
            }
            $dataDir->close();
        }

        return true;
    }
}