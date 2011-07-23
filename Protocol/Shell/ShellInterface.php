<?php

namespace BeSimple\DeploymentBundle\Protocol\Shell;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;
use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Commands;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
interface ShellInterface extends ProtocolInterface
{
    /**
     * Execute commands on distant server.
     *
     * @param Server $server Server configuration
     * @param Commands $commands Commands to execute
     * @return boolean Success (or not)
     */
    public function execute(Server $server, Commands $commands);
}
