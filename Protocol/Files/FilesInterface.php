<?php

namespace BeSimple\DeploymentBundle\Protocol\Files;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;
use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Transfert;

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
    public function transfert(Server $server, Transfert $transfert);
}
