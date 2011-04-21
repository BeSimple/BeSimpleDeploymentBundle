<?php

namespace BeSimple\DeploymentBundle\Command;

use Bundle\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Command\Command;

class LaunchDeploymentCommand extends DeploymentCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('deployment:launch');
    }

    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->launch($server);
    }
}
