<?php

namespace BeSimple\DeploymentBundle\Protocol\Shell;

use BeSimple\DeploymentBundle\Protocol\AbstractProtocol;
use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Command;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class SshProcess extends AbstractProtocol implements ShellInterface
{
    public function execute(Server $server, array $commands)
    {
        $this->startSession();

        $ok = true;
        foreach ($commands as $command) {
            if (!$this->executeProcess($this->buildCommand($server, $command))) {
                $ok = false;
            }
        }

        return $ok;
    }

    protected function buildCommand(Server $server, $command)
    {
        if ($server->getSshIdentity()->hasKey()) {
            return sprintf(
                'ssh %s@%s:%s -i %s -i %s %s',
                $server->getSshIdentity()->getUser(),
                $server->getHost(),
                $server->getSshPort(),
                $server->getSshIdentity()->getPrivateKeyFile(),
                $server->getSshIdentity()->getPublicKeyFile(),
                $command
            );
        } else {
            return sprintf(
                'spawn ssh %s@%s:%s %s; expect "*?assword:*"; send -- "%s\n"',
                $server->getSshIdentity()->getUser(),
                $server->getHost(),
                $server->getSshPort(),
                $command,
                $server->getSshIdentity()->getPassword()
            );
        }
    }
}
