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
        return $this->call('launch', $server);
    }

    public function test($server = null)
    {
        return $this->call('test', $server);
    }

    protected function call($method, $server = null)
    {
        if(is_null($server)) {
            foreach($this->config->getServers() as $server) {
                $this->call($method, $server);
            }
        } else {
            $this->dispatchEvent($method.'.start', $server);
            try {
                $deployer = $this->config->getDeployer($server);
                $scheduler = $this->config->getScheduler($server);
                $this->logger->logDeployment($this->config->getType($server), $deployer->$method($scheduler));
                $this->dispatchEvent($method.'.success', $server);
            } catch(\Exception $e) {
                $this->logger->logException($e);
                $this->dispatchEvent($method.'.fail', $server);
            }
        }
    }

    protected function dispatchEvent($event, $server)
    {
        // TODO: manage events
    }
}