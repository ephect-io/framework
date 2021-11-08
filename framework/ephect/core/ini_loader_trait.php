<?php 

namespace Ephect\Core;

use Ephect\Registry\Registry;

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
            Registry::write('application', 'name', $ini['application']['name']);
        }
        if(isset($ini['application']['title'])) {
            Registry::write('application', 'title', $ini['application']['title']);
        }
        
        foreach($ini as $key=>$values) {
            Registry::write('ini', $key, $values);
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
                    Registry::write('connections', $info->filename, $conf);
                }
            }
            $dataDir->close();
        }

        return true;
    }
}