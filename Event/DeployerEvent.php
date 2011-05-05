<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class DeployerEvent extends Event
{
    /**
     * @var string
     */
    protected $server;

    /**
     * @var boolean
     */
    protected $real;

    /**
     * @param string $server
     * @param boolean $real
     */
    public function __construct($server, $real)
    {
        $this->server = $server;
        $this->real   = $real;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return boolean
     */
    public function isTest()
    {
        return !$this->real;
    }
}
