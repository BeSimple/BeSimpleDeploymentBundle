<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class CommandEvent extends Event
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }
}
