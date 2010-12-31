<?php

namespace Bundle\DeploymentBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Logger
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function logDeployment(array $result = array())
    {
    }

    public function logException(\Exception $exception)
    {
    }
}