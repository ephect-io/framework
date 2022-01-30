<?php

namespace Ephect\Framework\Tasks;

interface TaskRunnerInterface
{
    public function run(): void;
    public function getResult();
    public function close(): void;
}