<?php

namespace Bundle\DeploymentBundle\Deployer;

use Bundle\DeploymentBundle\Service\Scheduler;

interface DeployerInterface
{
    public function __construct(array $connection);
    public function launch(Scheduler $scheduler);
    public function test(Scheduler $scheduler);
}