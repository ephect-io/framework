<?php

namespace Ephect\Tasks;

use parallel\{Runtime, Channel};

class TaskRunner implements TaskRunnerInterface
{
    protected $task = null;
    protected $channel = null;
    protected $result = null;

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
        $this->channel = new Channel;
    }

    public function run(): void
    {
        $argv = $this->task->getArguments();
        $argv[] = FRAMEWORK_ROOT;
        $argv[] = $this->channel;
        $threadFunc = $this->task->getCallback();
        $r1 = new Runtime;
        $r1->run($threadFunc, $argv);

        $this->result = $this->channel->recv();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function close(): void
    {
        $this->channel->close();
    }
}