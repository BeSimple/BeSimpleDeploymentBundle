<?php

namespace Bundle\DeploymentBundle\Deployer;

use Bundle\DeploymentBundle\Service\Rules;

interface DeployerInterface
{
    public function __construct(array $connection);
    public function deploy(Rules $rules);
}