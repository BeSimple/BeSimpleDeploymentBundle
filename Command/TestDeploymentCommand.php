<?php

namespace BeSimple\DeploymentBundle\Command;

use BeSimple\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Command\Command;

class TestDeploymentCommand extends DeploymentCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('deployment:test')
            ->setDescription('Test deployment')
        ;
    }

    /**
     * @param \BeSimple\DeploymentBundle\Deployer\Deployer $deployer
     * @param string $server
     * @return void
     */
    protected function executeDeployment(Deployer $deployer, $server)
    {
        $deployer->test($server);
    }
}
