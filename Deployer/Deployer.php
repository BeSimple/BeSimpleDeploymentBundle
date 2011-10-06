<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BeSimple\DeploymentBundle\Events;
use BeSimple\DeploymentBundle\Event\DeployerEvent;

class Deployer
{
    /**
     * @var Rsync
     */
    protected $rsync;

    /**
     * @var Ssh
     */
    protected $ssh;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param Rsync $rsync
     * @param Ssh $ssh
     * @param Config $config
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Rsync $rsync, Ssh $ssh, Config $config, EventDispatcherInterface $eventDispatcher)
    {
        $this->rsync      = $rsync;
        $this->ssh        = $ssh;
        $this->config     = $config;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param null|string $server
     * @return void
     */
    public function launch($server = null)
    {
        $this->deploy($server, true);
    }

    /**
     * @param null|string $server
     * @return void
     */
    public function test($server = null)
    {
        $this->deploy($server, false);
    }

    /**
     * @param null|string $server
     * @param bool $real
     * @return
     */
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
        if(false === empty($config['commands'])){
            $this->ssh->run($config['connection'], $config['commands'], $real);
        }

        $this->dispatcher->dispatch(Events::onDeploymentSuccess, new DeployerEvent($server, $real));
    }
}
