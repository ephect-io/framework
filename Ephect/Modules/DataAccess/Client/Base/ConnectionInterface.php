<?php

namespace Ephect\Modules\DataAccess\Client\Base;

interface ConnectionInterface
{
    public function getDriver();

    public function getState();

    public function open();

    public function close();
}
