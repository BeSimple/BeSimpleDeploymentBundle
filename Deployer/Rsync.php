<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;
use BeSimple\DeploymentBundle\Event\CommandEvent;
use BeSimple\DeploymentBundle\Event\FeedbackEvent;
use BeSimple\DeploymentBundle\Events;

class Rsync
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $stderr;

    /**
     * @var array
     */
    protected $stdout;

    /**
     * @var array|\Closure
     */
    protected $callback;

    /**
     * @param Logger $logger
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param array $config
     */
    public function __construct(Logger $logger, EventDispatcherInterface $eventDispatcher, array $config)
    {
        $this->logger     = $logger;
        $this->dispatcher = $eventDispatcher;
        $this->config     = $config;
        $this->stdout     = array();
        $this->stderr     = array();
        $this->callback   = null;
    }

    /**
     * @param string $glue
     * @return array|string
     */
    public function getStdout($glue = "\n")
    {
        if (!$glue) {
            return $this->stdout;
        }

        return implode($glue, $this->stdout);
    }

    /**
     * @param string $glue
     * @return array|string
     */
    public function getStderr($glue = "\n")
    {
        if (!$glue) {
            return $this->stderr;
        }

        return implode($glue, $this->stderr);
    }

    /**
     * @throws \Exception|\InvalidArgumentException
     * @param array $connection
     * @param array $rules
     * @param bool $real
     * @return array
     */
    public function run(array $connection, array $rules, $real = false)
    {
        $root = realpath($this->config['root']);

        if (!$root) {
            throw new \InvalidArgumentException(sprintf('Invalid "root" option : "%s" is not a valid path', $this->config['root']));
        }

        $command = $this->buildCommand($connection, $rules, $real);
        $process = new Process($command, $root);

        $this->stderr = array();
        $this->stdout = array();

        $this->dispatcher->dispatch(Events::onDeploymentRsyncStart, new CommandEvent($command));

        $code = $process->run(array($this, 'onStdLine'));

        if ($code !== 0) {
            throw new \Exception($this->stderr, $process->getExitCode());
        }

        $this->dispatcher->dispatch(Events::onDeploymentRsyncSuccess, new CommandEvent($command));

        return $this->stdout;
    }

    /**
     * @param string $type
     * @param string $line
     * @return void
     */
    public function onStdLine($type, $line)
    {
        if ('out' == $type) {
            $this->stdout[] = $line;
        } else {
            $this->stderr = $line;
        }

        $this->dispatcher->dispatch(Events::onDeploymentRsyncFeedback, new FeedbackEvent($type, $line));
    }

    /**
     * @param array $connection
     * @param array $rules
     * @param bool $real
     * @return string
     */
    protected function buildCommand(array $connection, array $rules, $real = false)
    {
        $source      = '.';
        $user        = $connection['username'] ? $connection['username'].'@' : '';
        $destination = sprintf('%s%s:%s', $user, $connection['host'], $connection['path']);
        $options     = array();
        $options[]   = $this->config['options'];

        if (!empty($connection['port'])) {
            $options[] = '-p '.$connection['port'];
        }

        if ($this->config['delete']) {
            $options[] = '--delete';
        }

        if (count($rules)) {
            $options[] = "--filter='+ *'";

            foreach ($rules['ignore'] as $mask) {
                $options[] = sprintf('-f -%s', $mask);
            }

            foreach ($rules['force'] as $mask) {
                $options[] = sprintf('-f +%s', $mask);
            }
        }

        return sprintf('%s -e ssh %s %s %s', $this->config['command'], implode(' ', $options), $source, $destination);
    }
}
