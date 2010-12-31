<?php

namespace Bundle\DeploymentBundle\Deployer;

use Bundle\DeploymentBundle\Deployer\Deployer;
use Bundle\DeploymentBundle\Deployer\DeployerInterface;
use Bundle\DeploymentBundle\Service\Scheduler;

class RsyncDeployer extends Deployer implements DeployerInterface
{
    public function deploy(Scheduler $scheduler)
    {
        return array();
    }
}