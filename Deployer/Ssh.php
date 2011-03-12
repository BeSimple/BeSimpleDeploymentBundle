<?php

namespace BeSimple\DeploymentBundle\Deployer;

class Ssh
{
    protected $logger;
    protected $config;
    protected $session;
    protected $shell;
    protected $stdout;
    protected $stderr;

    public function __construct(Logger $logger, array $config)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->session = null;
        $this->stdout = array();
        $this->stdin = array();
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
        $this->execute(sprintf('cd %s', $connection['path']));

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

        if ($connection['username'] && $connection['pubkey_file'] && $connection['privkey_file']) {
            if (!ssh2_auth_pubkey_file($connection['username'], $connection['pubkey_file'], $connection['privkey_file'], $connection['passphrase'])) {
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s" with public key "%s"', $connection['username'], $connection['pubkey_file']));
            }
        } else if ($connection['username'] && $connection['password']) {
            if (!ssh2_auth_password($this->session, $connection['username'], $connection['password'])) {
                throw new \InvalidArgumentException(sprintf('SSH authentication failed for user "%s"', $connection['username']));
            }
        }

        $this->shell = ssh2_shell($this->session, $this->config['shell']);

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

    protected function execute($command)
    {
        $outStream = ssh2_exec($connection, $command);
        $errStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($outStream, true);
        stream_set_blocking($errStream, true);

        $this->stdout = array_merge($this->stdout, explode("\n", stream_get_contents($outStream)));
        $this->stderr = array_merge($this->stderr, explode("\n", stream_get_contents($errStream)));

        fclose($outStream);
        fclose($errStream);
    }
}