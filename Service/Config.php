<?php

namespace Bundle\DeploymentBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Bundle\DeploymentBundle\Deployer\RsyncDeployer;
use Bundle\DeploymentBundle\Deployer\FtpDeployer;

class Config
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getServers()
    {
        return $this->container->getParameter('deployment.servers.list');
    }

    public function getDeployer($server)
    {
        $type = strtolower((string) $this->getType($server));
        $connection = $this->getConnection($server);

        switch($type) {
            case 'rsync': return new RsyncDeployer($connection);
            case 'ftp': return new FtpDeployer($connection);
            default: throw new \InvalidArgumentException('Unknown deployer type "'.$type.'"');
        }
    }



    public function getScheduler($server)
    {
        $parameters = $this->getParameters($server);
        $rules = $this->getRules();
    }

    protected function getParameters($server)
    {
        if(! $this->container->hasParameter('deployment.server.'.$server)) {
            throw new \InvalidArgumentException('Unknown server name "'.$server.'"');
        }

        return array_merge(
            $this->container->getParameter('deployment.servers.defaults'),
            $this->container->getParameter('deployment.server.'.$server)
        );
    }
}