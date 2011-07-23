<?php

namespace BeSimple\DeploymentBundle\Protocol\Exceptions;

use BeSimple\DeploymentBundle\Model\Server;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class AvailabilityException
{
    protected $protocol;
    protected $server;

    public function __constrcut($protocol, Server $server)
    {
        $this->protocol = $protocol;
        $this->server   = $server;
    }

    public function getMessage()
    {
        return sprintf('%s protocol is not available for server %s', ucfirst($this->protocol), $this->server->getName());
    }
}
