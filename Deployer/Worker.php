<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Worker
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function dispatchEvent($name, array $parameters = array())
    {

    }
}
