<?php
namespace Ephect\Commands;

interface CommandInterface
{
    function getCommand(): CommandStructure;
}
