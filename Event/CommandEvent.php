<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class CommandEvent extends Event
{
    protected $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
