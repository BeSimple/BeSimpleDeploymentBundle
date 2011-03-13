<?php

namespace Bundle\DeploymentBundle\Deployer;

use Symfony\Bundle\FrameworkBundle\EventDispatcher;

class Deployer
{
    protected $rsync;
    protected $ssh;
    protected $config;
    protected $eventDispatcher;

    public function __construct(Rsync $rsync, Ssh $ssh, Config $config, EventDispatcher $eventDispatcher)
    {
        $this->rsync = $rsync;
        $this->ssh = $ssh;
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function launch($server = null)
    {
        return $this->call($server, true);
    }

    public function test($server = null)
    {
        return $this->call($server, false);
    }

    protected function call($server = null, $real = false)
    {
        if(is_null($server)) {
            foreach($this->config->getServerNames() as $server) {
                $this->call($server, $real);
            }

            return;
        }

        $this->dispatchEvent('start', array('server' => $server, 'real' => $real));

        try {
            $config = $this->config->getServerConfig($server);
            $rsync = $this->rsync->run($config['connection'], $config['rules'], $real);
            $ssh = $this->ssh->run($config['connection'], $config['commands'], $real);
        }

        catch(\Exception $e) {
            $this->dispatchEvent('error', array('server' => $server, 'method' => $method, 'exception' => $e));
        }

        $this->dispatchEvent('success', array('server' => $server, 'real' => $real, 'rsync' => $rsync, 'ssh' => $ssh));
    }

    protected function dispatchEvent($name, array $parameters)
    {
        $this->eventDispatcher->notify(new Event($this, 'besimple_deployment.'.$name, $parameters));
    }
}