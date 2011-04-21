<?php

namespace BeSimple\DeploymentBundle\Command;

use Bundle\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Command\Command;

abstract class TestDeploymentCommand extends DeploymentCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('deployment:test');
    }

    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->test($server);
    }
}
