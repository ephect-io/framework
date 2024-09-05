<?php

namespace Ephect\Modules\DataAccess;

interface ConnectionInterface
{
    public function getDriver();

    public function getState();

    public function open();

    public function close();
}
