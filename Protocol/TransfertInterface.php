<?php

namespace BeSimple\DeploymentBundle\Protocol;

use BeSimple\DeploymentBundle\Deployer\Deployment;
use BeSimple\DeploymentBundle\Deployer\Server;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
interface TransfertInterface extends ProtocolInterface
{
    /**
     * Deploy project on distant server.
     *
     * @param Deployment $deployment Deployment configuration
     * @param Server $server Server configuration
     * @return boolean Success (or not)
     */
    public function transfert(Deployment $deployment, Server $server);
}
