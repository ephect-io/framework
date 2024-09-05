<?php
namespace Ephect\Framework\Configuration;

use Ephect\Framework\Element;

abstract class AbstractConfiguration extends Element implements ConfigurableInterface
{
    protected $filename = '';
    protected $canConfigure = false;
    
    public function loadConfiguration(string $filename) : bool
    {
        $this->canConfigure = file_exists($filename);

        if(!$this->canConfigure) {
            return false;
        }

        $this->filename = $filename;
        $this->configure();
        
        return true;
    }
}
