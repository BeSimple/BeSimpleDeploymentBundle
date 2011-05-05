<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BeSimple\DeploymentBundle\Events;
use BeSimple\DeploymentBundle\Event\DeployerEvent;

class Deployer
{
    protected $rsync;
    protected $ssh;
    protected $config;
    protected $dispatcher;

    public function __construct(Rsync $rsync, Ssh $ssh, Config $config, EventDispatcherInterface $eventDispatcher)
    {
        $this->rsync      = $rsync;
        $this->ssh        = $ssh;
        $this->config     = $config;
        $this->dispatcher = $eventDispatcher;
    }

    public function launch($server = null)
    {
        $this->deploy($server, true);
    }

    public function test($server = null)
    {
        $this->deploy($server, false);
    }

    protected function deploy($server = null, $real = false)
    {
        if(is_null($server)) {
            foreach($this->config->getServerNames() as $server) {
                $this->deploy($server, $real);
            }

            return;
        }

        $this->dispatcher->dispatch(Events::onDeploymentStart, new DeployerEvent($server, $real));

        $config = $this->config->getServerConfig($server);

        $this->rsync->run($config['connection'], $config['rules'], $real);
        $this->ssh->run($config['connection'], $config['commands'], $real);

        $this->dispatcher->dispatch(Events::onDeploymentSuccess, new DeployerEvent($server, $real));
    }
}
