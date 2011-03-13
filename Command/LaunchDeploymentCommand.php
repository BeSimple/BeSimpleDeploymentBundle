<?php

namespace BeSimple\DeploymentBundle\Command;

use Bundle\DeploymentBundle\Deployer\Deployer;

abstract class DeploymentCommand extends Command
{
    protected function configure()
    {
        $this->setName('deployment:launch');
    }

    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->launch($server);
    }
}