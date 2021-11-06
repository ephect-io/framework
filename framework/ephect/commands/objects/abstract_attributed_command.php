<?php
namespace Ephect\Commands;

use Ephect\CLI\Application;
use Ephect\Element;

abstract class AbstractAttributedCommand extends Element implements AttributedCommandInterface
{

    public function __construct(protected Application $application)
    {
        parent::__construct();

    }



}
