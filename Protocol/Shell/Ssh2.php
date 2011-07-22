<?php

namespace BeSimple\DeploymentBundle\Protocol\Shell;

use BeSimple\DeploymentBundle\Protocol\AbstractProtocol;
use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Command;
use BeSimple\DeploymentBundle\Model\Identity;
use BeSimple\DeploymentBundle\Exceptions\ConnectionException;
use BeSimple\DeploymentBundle\Exceptions\AuthenticationException;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Ssh2 extends AbstractProtocol implements ShellInterface
{
    public function execute(Server $server, array $commands)
    {
        list($session, $shell) = $this->connect($server);

        foreach ($commands as $command) {
            $output = ssh2_exec($session, $command);
            $errors = ssh2_fetch_stream($output, SSH2_STREAM_STDERR);

            $this->addStream($output, false);
            $this->addStream($errors, true);
        }

        $this->disconnect($shell);

        return !$errors;
    }

    protected function connect(Server $server)
    {
        if (!$session = ssh2_connect($server->getHost(), $server->getSshPort())) {
            throw new ConnectionException($this, $server->getHost(), $server->getSshPort());
        }

        if (!$this->authenticate($session, $server->getSshIdentity())) {
            throw new AuthenticationException($this, $server->getHost(), $server->getSshIdentity());
        }

        if (!$shell = ssh2_shell($session)) {
            throw new ProtocolException($this, 'Failed opening shell');
        }

        return array($session, $shell);
    }

    protected function disconnect($shell)
    {
        fclose($shell);
    }

    protected function addStream($output, $isError)
    {
        stream_set_blocking($output, true);

        foreach (explode("\n", stream_get_contents($output)) as $message){
            $this->addOutput(new Output($message, $isError));
        }

        fclose($output);
    }

    protected function authenticate($session, Identity $identity)
    {
        if ($identity->hasKey() && ssh2_auth_pubkey_file($session, $identity->getUser(), $identity->getPublicKeyFile(), $identity->getPrivateKeyFile(), $identity->getKeyPassphrase())) {
            return true;
        }

        return ssh2_auth_password($session, $identity->getUser(), $identity->getPassword());
    }
}
