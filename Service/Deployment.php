<?php

namespace Bundle\DeploymentBundle\Service;

use Bundle\DeploymentBundle\Service\Config;

class Deployment
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function launch($server = null)
    {
        return $this->call('deploy', $server);
    }

    public function test($server = null)
    {
        return $this->call('test', $server);
    }

    protected function call($method, $server = null)
    {
        if(is_null($server)) {
            foreach($this->config->getServers() as $server) {
                $this->deploy($server);
            }
        } else {
            try {
                $deployer = $this->config->getDeployer($server);
                $scheduler = $this->config->getScheduler($server);
                $this->logger->logDeployment($deployer->$method($scheduler));
            } catch(\Exception $e) {
                $this->logger->logException($e);
            }
        }
    }
}