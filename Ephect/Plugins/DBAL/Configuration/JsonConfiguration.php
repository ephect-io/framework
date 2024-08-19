<?php
namespace Ephect\Plugins\DBAL\Configuration;

use Ephect\Framework\Configuration\AbstractConfiguration;

class JsonConfiguration extends AbstractConfiguration
{
    protected array $contents = [];

    public function configure() : void
    {
        $text = file_get_contents($this->filename);
        $this->contents = json_decode($text, true);
    }
}
