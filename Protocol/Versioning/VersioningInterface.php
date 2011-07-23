<?php

namespace BeSimple\DeploymentBundle\Protocol\Versioning;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;
use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Deployment;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
interface VersioningInterface extends ProtocolInterface
{
    /**
     * Update project on distant server.
     *
     * @param  Server  $server     Server configuration
     * @param  string  $deployment Branch, tag or URL
     * @return boolean             Success (or not)
     */
    public function checkout(Server $server, $source);
}
