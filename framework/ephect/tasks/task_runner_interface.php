<?php

namespace Ephect\Tasks;

interface TaskRunnerInterface
{
    public function run(): void;
    public function getResult();
    public function close(): void;
}