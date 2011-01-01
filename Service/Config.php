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
        return array_keys($this->container->getParameter('deployment.servers'));
    }

    public function getType($server)
    {
        $config = $this->getConfig($server);
        $default = $this->container->getParameter('deployment.defaults');

        return strtolower(isset($config['type']) ? $config['type'] : $default['type']);
    }

    public function getDeployer($server)
    {
        $config = $this->getConfig($server);
        $default = $this->container->getParameter('deployment.defaults');

        $type = strtolower(isset($config['type']) ? $config['type'] : $default['type']);
        $connection = array();

        foreach(self::getConnectionParameters() as $parameter) {
            $connection[$parameter] = isset($config['connection'][$parameter]) ? $config['connection'][$parameter] : $default[$parameter];
        }

        switch($type) {
            case 'rsync': return new RsyncDeployer($connection);
            case 'ftp': return new FtpDeployer($connection);
            default: throw new \InvalidArgumentException('Unknown deployer type "'.$type.'"');
        }
    }

    public function getScheduler($server)
    {
        $templates = $this->container->getParameter('deployment.rules');
        $config = $this->getConfig($server);
        $scheduler = new Scheduler();

        foreach($config['rules'] as $name) {
            $scheduler->addRule($name, $templates[$name]);
        }

        $scheduler->addRule('_', $config['rule']);

        return $scheduler;
    }

    public static function getConnectionParameters()
    {
        return array('host', 'username', 'password', 'path');
    }

    public static function getRuleParameters()
    {
        return array('ignore', 'force', 'commands');
    }

    protected function getConfig($server)
    {
        $servers = $this->container->getParameter('deployment.servers');
        return $servers[$server];
    }
}