<?php

namespace BeSimple\DeploymentBundle\Protocol;

use Symfony\Component\Process\Process;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class AbstractProtocol
{
    protected $dispatcher;
    protected $outputs;

    public function __construct(EventDispatcher $dispatcher, $rootDir)
    {
        $this->dispatcher = $dispatcher;
        $this->rootDir    = $rootDir;
        $this->outputs    = array();
    }

    public function getOutputs()
    {
        return $this->outputs;
    }

    public function addOutput(Output $output)
    {
        $this->outputs[] = $output;

        $this->dispatcher->dispatch('onOutput', new OutputEvent($this, $output));
    }

    protected function startSession()
    {
        $this->outputs = array();
    }

    protected function executeProcess($command, $cwd = null)
    {
        $that     = $this;
        $callback = function($type, $line) use ($that) { $that->addOutput(new Output($line, $type !== 'ou')); };
        $process  = new Process($command, $cwd ?: $this->rootDir);
        $status   = $process->run($callback);

        return $status === 0;
    }
}
