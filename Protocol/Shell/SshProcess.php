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
        $this->connect($server);

        $ok = true;
        foreach ($commands as $command) {
            if (!$this->executeProcess($command)) {
                $ok = false;
            }
        }

        $this->disconnect();

        return $ok;
    }

    protected function connect(Server $server)
    {
        $this->startSession();

        if ($server->getSshIdentity()->hasKey()) {
            $command = '';
        } else {
            $command = '';
        }

        return $this->executeProcess($command);
    }

    protected function disconnect()
    {
        return $this->executeProcess('exit');
    }
}
