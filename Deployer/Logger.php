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

    public function __construct($logger)
    {
        $this->logger = $logger;
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
    }
}