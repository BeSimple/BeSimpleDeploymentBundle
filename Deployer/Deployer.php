<?php

namespace Bundle\DeploymentBundle\Deployer;

abstract class Deployer
{
    protected $connection;

    public function __construct(array $connection)
    {
        $this->connection = $connection;
    }
}