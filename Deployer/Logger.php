<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Logger
{
    const ERROR = 'err';
    const INFO  = 'info';

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $type
     * @param string $server
     * @param int $count
     * @return void
     */
    public function logDeployment($type, $server, $count)
    {
        $this->log(self::INFO, sprintf('%s files deployed via %s on %s', $count, $type, $server));
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function logException(\Exception $exception)
    {
        $this->log(self::ERROR, $exception->getMessage());
    }

    /**
     * @param string $type
     * @param string $message
     * @return void
     */
    protected function log($type, $message)
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->$type($message);
        }
    }
}
