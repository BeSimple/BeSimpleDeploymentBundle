<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Bundle\FrameworkBundle\EventDispatcher;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Logger
{
    const ERROR = 'err';
    const INFO  = 'info';

    protected $logger;
    protected $eventDispatcher;

    public function __construct($logger, EventDispatcher $eventDispatcher)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function logDeployment($type, $server, $count)
    {
        $this->log(self::INFO, sprintf('%s files deployed via %s on %s', $count, $type, $server));
    }

    public function logException(\Exception $exception)
    {
        $this->log(self::ERROR, $exception->getMessage());
    }

    protected function log($type, $message)
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->$type($message);
        }

        $this->eventDispatcher->notify(new Event(
            $this,
            'besimple_deployment.log',
            array('type' => $type, 'message' => $message)
        ));
    }
}