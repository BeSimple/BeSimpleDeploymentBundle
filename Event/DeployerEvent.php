<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class DeployerEvent extends Event
{
    protected $server;
    protected $real;

    public function __construct($server, $real)
    {
        $this->server = $server;
        $this->real   = $real;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function isTest()
    {
        return !$this->real;
    }
}
