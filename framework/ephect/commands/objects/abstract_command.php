<?php
namespace Ephect\Commands;

use Ephect\CLI\Application;
use Ephect\Element;

abstract class AbstractCommand extends Element implements CommandInterface
{

    public function __construct(protected Application $application)
    {
        parent::__construct();

    }



}
