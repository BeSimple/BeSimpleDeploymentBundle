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

    public function logDeployment($type, array $result)
    {
        $method = 'log'.ucfirst($type).'Deployment';
        $this->$method($result);
    }

    public function logException(\Exception $exception)
    {
    }

    protected function logRsyncDeployment(array $result)
    {
    }

    protected function logFtpDeployment(array $result)
    {
    }

    protected function log($message, $type)
    {
        // TODO: manage log
    }
}