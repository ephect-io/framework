<?php

namespace Ephect\Modules\DataAccess\Configuration;

use Ephect\Framework\Configuration\AbstractConfiguration;
use Iterator;

class FileConfiguration extends AbstractConfiguration
{
    //put your code here
    protected array $innerList = [];

    public function configure(): void
    {
        $this->innerList = file($this->filename);
    }

    public function readLine(): Iterator
    {
        return yield $this->innerList;
    }
}
