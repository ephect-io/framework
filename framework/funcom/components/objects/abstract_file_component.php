<?php

namespace FunCom\Components;

use FunCom\IO\Utils;

class AbstractFileComponent  extends AbstractComponent implements FileComponentInterface
{

    protected $filename = '';

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public static function getCacheFilename(string $basename): string
    {
        $cache_file = str_replace('/', '_', $basename);

        return $cache_file;
    }
    
    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(CACHE_DIR . $this->filename);
        if($this->code === null) {
            $this->code = Utils::safeRead(SRC_ROOT . $this->filename);
        }

        list($this->namespace, $this->function) = $this->getFunctionDefinition();
        $result = $this->code !== null;

        return  $result;
    }


  
}
