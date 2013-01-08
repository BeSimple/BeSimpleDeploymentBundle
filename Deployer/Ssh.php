<?php

namespace BeSimple\DeploymentBundle\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BeSimple\DeploymentBundle\Event\CommandEvent;
use BeSimple\DeploymentBundle\Event\FeedbackEvent;
use BeSimple\DeploymentBundle\Events;

class Ssh
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
     * @var resource
     */
    protected $session;

    /**
     * @var resource
     */
    protected $shell;

    /**
     * @var array
     */
    protected $stdout;

    /**
     * @var array
     */
    protected $stderr;

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
        $this->session    = null;
        $this->shell      = null;
        $this->stdout     = array();
        $this->stdin      = array();
        $this->stderr     = array();
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
     * @param array $connection
     * @param array $commands
     * @param bool $real
     * @return array
     */
    public function run(array $connection, array $commands, $real = false)
    {
        $this->connect($connection);

        if($real){
            foreach($commands as $command){
                $this->execute($command, $connection);
            }
        }

        return $this->stdout;
    }

    /**
     * @throws \InvalidArgumentException|\RuntimeException
     * @param array $connection
     * @return void
     */
    protected function connect(array $connection)
    {
        $methods = @$connection['connect_methods'] ?: @$this->config['connect_methods'];

        $this->session = @ssh2_connect($connection['host'], $connection['ssh_port'], $methods);
        if (!$this->session) {
            throw new \InvalidArgumentException(sprintf('SSH connection failed on "%s:%s"', $connection['host'], $connection['ssh_port']));
        }

        $username = @$connection['username'] ?: @$this->config['username'];
        $password = @$connection['password'] ?: @$this->config['password'];

        if($username && $password){
            if(!ssh2_auth_password($this->session, $username, $password)){
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s"', $username));
            }
        }elseif($username){
            $pubkey     = @$connection['pubkey_file'] ?: @$this->config['pubkey_file'];
            $privkey    = @$connection['privkey_file'] ?: @$this->config['privkey_file'];
            $passphrase = @$connection['passphrase'] ?: @$this->config['passphrase'];

            if(!ssh2_auth_pubkey_file($this->session, $username, $pubkey, $privkey, $passphrase)){
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s" with public key "%s"', $username, $pubkey));
            }
        }

        $this->stdout = array();
        $this->stdin = array();
    }

    /**
     * @param array $command
     * @param array $connection
     */
    protected function execute(array $command, array $connection)
    {
        $command = $this->buildCommand($command, $connection);
        $this->dispatcher->dispatch(Events::onDeploymentSshFeedback, new FeedbackEvent('out', 'Trying: '. $command));

        $this->dispatcher->dispatch(Events::onDeploymentSshStart, new CommandEvent($command));

        $outStream = ssh2_exec($this->session, $command, "xterm");
        $errStream = ssh2_fetch_stream($outStream, SSH2_STREAM_STDERR);

        stream_set_blocking($outStream, true);
        stream_set_blocking($errStream, true);

        $stdout = stream_get_contents($outStream);
        $stderr = stream_get_contents($errStream);

        if($stdout){
            $this->dispatcher->dispatch(Events::onDeploymentSshFeedback, new FeedbackEvent('out', $stdout));
        }

        if($stderr){
            $this->dispatcher->dispatch(Events::onDeploymentSshFeedback, new FeedbackEvent('err', $stderr));
        }

        if(!$stderr){
            $this->dispatcher->dispatch(Events::onDeploymentSshSuccess, new CommandEvent($command));
        }

        fclose($outStream);
        fclose($errStream);
    }

    /**
     * @param array $command
     * @return string
     */
    protected function buildCommand(array $command, array $connection)
    {
        switch($command['type']){
            case 'shell':
                return $command['command'];
            break;
            case 'symfony':
                return sprintf('cd %s && %s %s', $connection['path'], $connection['symfony_command'], $command['command']);
            break;
        }

        throw new \InvalidArgumentException(sprintf('CommandType "%s" invalid', $command['type']));
    }
}
