<?php

namespace BeSimple\DeploymentBundle\Command;

use Bundle\DeploymentBundle\Deployer\Deployer;

abstract class DeploymentCommand extends Command
{
    protected function configure()
    {
        $this->setName('deployment:test');
    }

    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->test($server);
    }
}