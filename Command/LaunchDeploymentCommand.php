<?php

namespace BeSimple\DeploymentBundle\Command;

use BeSimple\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Command\Command;

class LaunchDeploymentCommand extends DeploymentCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('deployment:launch')
            ->setDescription('Launch deployment')
        ;
    }

    /**
     * @param \BeSimple\DeploymentBundle\Deployer\Deployer $deployer
     * @param string $server
     * @return void
     */
    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->launch($server);
    }
}
