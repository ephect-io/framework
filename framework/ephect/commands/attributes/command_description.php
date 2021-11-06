<?php
namespace Ephect\Commands\Attributes;

use Attribute;

#[Attribute]
class CommandDescription
{
    public function __construct(public string $description)
    {
        
    }
}