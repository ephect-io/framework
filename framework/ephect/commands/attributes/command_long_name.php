<?php
namespace Ephect\Commands\Attributes;

use Attribute;

#[Attribute]
class CommandLongName
{
    public function __construct(public string $longName)
    {
        
    }
}