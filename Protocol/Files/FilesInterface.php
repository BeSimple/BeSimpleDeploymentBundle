<?php

namespace BeSimple\DeploymentBundle\Protocol;

use BeSimple\DeploymentBundle\Deployer\Deployment;
use BeSimple\DeploymentBundle\Deployer\Server;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
interface FilesInterface extends ProtocolInterface
{
    /**
     * Deploy project on distant server.
     *
     * @param Server $server Server configuration
     * @param Deployment $deployment Deployment configuration
     * @return boolean Success (or not)
     */
    public function transfert(Server $server, Deployment $deployment);
}
