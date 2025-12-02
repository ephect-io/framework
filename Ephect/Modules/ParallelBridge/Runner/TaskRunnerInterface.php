<?php

namespace Ephect\Modules\ParallelBridge\Runner;

interface TaskRunnerInterface
{
    public function run(): void;
    public function getResult();
    public function close(): void;
}