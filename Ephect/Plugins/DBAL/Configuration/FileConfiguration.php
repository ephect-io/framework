<?php
namespace Ephect\Plugins\DBAL\Data;

use Ephect\Framework\Configuration\AbstractConfiguration;

class FileConfiguration extends AbstractConfiguration
{
    //put your code here
    protected array $innerList = [];
    
    public function configure() : void
    {
        $this->innerList = file($this->filename);
    }

    public function readLine() : \Iterator
    {
        return yield $this->innerList;
    }
}
