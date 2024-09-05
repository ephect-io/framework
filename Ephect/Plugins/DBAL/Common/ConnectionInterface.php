<?php
namespace Ephect\Plugins\DBAL;

interface ConnectionInterface {
    public function getDriver();
    public function getState();
    public function open();
    public function close();
}
