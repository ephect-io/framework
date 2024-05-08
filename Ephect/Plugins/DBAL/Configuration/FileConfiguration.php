<?php
namespace Ephect\Plugins\DBAL\Data;

use Ephect\Framework\Configuration\AbstractConfiguration;

class FileConfiguration extends AbstractConfiguration
{
    //put your code here
    protected $innerList = [];
    
    public function configure() : void
    {
        $this->innerList = file($this->filename);
    }

    public function readLine() : \Iterator
    {
        $result = yield $this->innerList;

//        $result = each($this->innerList);
//        if (!$result) {
//            reset($this->innerList);
//        }

        return $result;
    }
}
