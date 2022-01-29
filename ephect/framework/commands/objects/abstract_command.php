<?php
namespace Ephect\Framework\Commands;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\Element;

abstract class AbstractCommand extends Element implements CommandInterface
{

    public function __construct(protected Application $application)
    {
        parent::__construct();

    }



}
