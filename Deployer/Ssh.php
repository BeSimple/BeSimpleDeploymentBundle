<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BeSimple\DeploymentBundle\Event\CommandEvent;
use BeSimple\DeploymentBundle\Event\FeedbackEvent;
use BeSimple\DeploymentBundle\Events;

class Ssh
{
    protected $logger;
    protected $dispatcher;
    protected $config;
    protected $session;
    protected $shell;
    protected $stdout;
    protected $stderr;

    public function __construct(Logger $logger, EventDispatcherInterface $eventDispatcher, array $config)
    {
        $this->logger     = $logger;
        $this->dispatcher = $eventDispatcher;
        $this->config     = $config;
        $this->session    = null;
        $this->shell      = null;
        $this->stdout     = array();
        $this->stdin      = array();
        $this->stderr     = array();
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

    public function run(array $connection, array $commands, $real = false)
    {
        $this->connect($connection);
        $this->execute(array('type' => 'shell', 'command' => sprintf('cd %s', $connection['path'])));

        if ($real) {
            foreach ($commands as $command) {
                $this->execute($command);
            }
        }

        $this->disconnect();

        return $this->stdout;
    }

    protected function connect(array $connection)
    {
        $this->session = ssh2_connect($connection['host'], $connection['ssh_port']);

        if (!$this->session) {
            throw new \InvalidArgumentException(sprintf('SSH connection failed on "%s:%s"', $connection['host'], $connection['ssh_port']));
        }

        if (isset($connection['username']) && isset($connection['pubkey_file']) && isset($connection['privkey_file'])) {
            if (!ssh2_auth_pubkey_file($connection['username'], $connection['pubkey_file'], $connection['privkey_file'], $connection['passphrase'])) {
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s" with public key "%s"', $connection['username'], $connection['pubkey_file']));
            }
        } else if ($connection['username'] && $connection['password']) {
            if (!ssh2_auth_password($this->session, $connection['username'], $connection['password'])) {
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s"', $connection['username']));
            }
        }

        $this->shell = ssh2_shell($this->session);

        if (!$this->shell) {
            throw new \RuntimeException(sprintf('Failed opening "%s" shell', $this->config['shell']));
        }

        $this->stdout = array();
        $this->stdin = array();
    }

    protected function disconnect()
    {
        fclose($this->shell);
    }

    protected function execute(array $command)
    {
        $command = $this->buildCommand($command);

        $this->dispatcher->dispatch(Events::onDeploymentSshStart, new CommandEvent($command));

        $outStream = ssh2_exec($this->session, $command);
        $errStream = ssh2_fetch_stream($outStream, SSH2_STREAM_STDERR);

        stream_set_blocking($outStream, true);
        stream_set_blocking($errStream, true);

        $stdout = explode("\n", stream_get_contents($outStream));
        $stderr = explode("\n", stream_get_contents($errStream));

        if (count($stdout)) {
            $this->dispatcher->dispatch(Events::onDeploymentRsyncFeedback, new FeedbackEvent('out', implode("\n", $stdout)));
        }

        if (count($stdout)) {
            $this->dispatcher->dispatch(Events::onDeploymentRsyncFeedback, new FeedbackEvent('err', implode("\n", $stderr)));
        }

        $this->stdout = array_merge($this->stdout, $stdout);

        if (is_array($stderr)) {
            $this->stderr = array_merge($this->stderr, $stderr);
        } else {
            $this->dispatcher->dispatch(Events::onDeploymentSshSuccess, new CommandEvent($command));
        }

        fclose($outStream);
        fclose($errStream);
    }

    protected function buildCommand(array $command)
    {
        if ($command['type'] === 'shell') {
            return $command['command'];
        }

        $symfony = $this->config['symfony_command'];
        $env = $command['env'] ?: $this->env;

        return sprintf('%s %s --env="%s"', $symfony, $command['command'], $env);
    }
}
