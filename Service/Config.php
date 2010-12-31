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
        $type = strtolower((string) $this->getParameter($server, 'type'));
        $connection = $this->getParameter($server, 'connection');

        switch($type) {
            case 'rsync': return new RsyncDeployer($connection);
            case 'ftp': return new FtpDeployer($connection);
            default: throw new \InvalidArgumentException('Unknown deployer type "'.$type.'"');
        }
    }

    public function getScheduler($server)
    {
        $scheduler = new Scheduler();

        try {
            $scheduler->addRule('ignore', $this->getParameter($server, 'ignore'));
        }
        catch(\InvalidArgumentException $e) {}

        try {
            $templates = $this->getParameter($server, 'rules');

            foreach($templates as $template) {
                try{
                    $template = $this->getRuleTemplate($template);
                    $scheduler->addRules($template);
                }
                catch(\InvalidArgumentException $e) {}
            }
        }
        catch(\InvalidArgumentException $e) {}

        return $scheduler;
    }

    protected function getRuleTemplate($name)
    {
        $key = 'deployment.rule.'.$name;

        if(! $this->container->hasParameter($key)) {
            throw new \InvalidArgumentException('Unknown rule template "'.$name.'"');
        }

        return $this->container->getParameter($key);
    }

    protected function getParameter($server, $parameter)
    {
        $key = 'deployment.server.'.$server.'.'.$parameter;

        if(! $this->container->hasParameter($key)) {
            throw new \InvalidArgumentException('Unknown server name "'.$server.'"');
        }

        return $this->container->getParameter($key);
    }
}