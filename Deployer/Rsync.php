<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Bundle\FrameworkBundle\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class Rsync
{
    protected $logger;
    protected $eventDispatcher;
    protected $config;
    protected $stderr;
    protected $stdout;
    protected $callback;

    public function __construct(Logger $logger, EventDispatcher $eventDispatcher, array $config)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
        $this->stdout = array();
        $this->stderr = array();
        $this->callback = null;
    }

    public function getStdout($glue = "\n")
    {
        if (!$glue) {
            return $this->stdout;
        }

        return implode($glue, $this->stdout);
    }

    public function getStderr($glue = "\n")
    {
        if (!$glue) {
            return $this->stderr;
        }

        return implode($glue, $this->stderr);
    }

    public function run(array $connection, array $rules, $real = false)
    {
        $root = realpath($this->config['root']);

        if (!$root) {
            throw new \InvalidArgumentException(sprintf('Invalid "root" option : "%s" is not a valid path', $this->config['root']));
        }

        $command = $this->buildCommand($connection, $rules, $real);
        $process = new Process($commant, $root);

        $this->stderr = array();
        $this->stdout = array();

        $code = $process->run(array($this, 'onStdLine'));

        if ($code !== 0) {
            throw new \RuntimeErrorException($this->stderr, $process->getExitCode());
        }

        return $this->stdout;
    }

    public function onStdLine($type, $line)
    {
        if ('out' == $type) {
            $this->stdout[] = $line;
        } else {
            $this->stderr = $line;
        }

        $this->eventDispatcher->notify(new Event($this, 'besimple_deployer.rsync', array('line' => $line, 'type' => $type)));
    }

    protected function buildCommand(array $connection, array $rules, $real = false)
    {
        $source = '.';
        $user = $connection['username'] ? $connection['username'].'@' : '';
        $destination = sprintf('%s%s:%s', $user, $connection['host'], $connection['path']);
        $options = $this->config['options'];

        if ($connection['port']) {
            $options[] = '-p '.$connection['port'];
        }

        if ($this->options['delete']) {
            $options[] = '--delete';
        }

        if (count($rules)) {
            $options[] = '-f +*';

            foreach ($rules['ignore'] as $mask) {
                $options[] = sprintf('-f -%s', $mask);
            }

            foreach ($rules['force'] as $mask) {
                $options[] = sprintf('-f +%s', $mask);
            }
        }

        return sprintf('%s -e ssh %s %s %s', $this->config['command'], implode(' ', $options), $source, $detination);
    }
}