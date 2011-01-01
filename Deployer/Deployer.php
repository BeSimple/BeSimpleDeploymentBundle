<?php

namespace Bundle\DeploymentBundle\Deployer;

use Bundle\DeploymentBundle\Service\Scheduler;

abstract class Deployer
{
    protected $connection;

    public function __construct(array $connection)
    {
        $this->connection = $connection;
    }

    protected function executeCommands(Scheduler $scheduler)
    {
        foreach($scheduler->getCommands() as $command) {
            // TODO: execute command
        }
    }

    protected function getFiles(Scheduler $scheduler)
    {
        // TODO: list files
    }
}