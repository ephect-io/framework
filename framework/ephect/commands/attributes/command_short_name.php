<?php
namespace Ephect\Commands\Attributes;

use Attribute;

#[Attribute]
class CommandShortName
{
    public function __construct(public string $shortName)
    {
        
    }
}