<?php

namespace Bundle\DeploymentBundle\Deployer;

use Bundle\DeploymentBundle\Service;

use Bundle\DeploymentBundle\Deployer\Deployer;
use Bundle\DeploymentBundle\Deployer\DeployerInterface;
use Bundle\DeploymentBundle\Service\Scheduler;

class FtpDeployer extends Deployer implements DeployerInterface
{
    protected $ftp;

    public function launch(Scheduler $scheduler)
    {
        $this->deploy($scheduler, true);
    }

    public function test(Scheduler $scheduler)
    {
        $this->deploy($scheduler, false);
    }

    protected function deploy(Scheduler $scheduler, $real)
    {
        $error = $this->login();
        if($error) {
            return array($error);
        }

        $result = array();
        foreach($this->getFiles($scheduler) as $file) {
            if($real) {
                $result[] = $this->upload($file);
            } else {
                $result[] = 'Test: '.$file;
            }
        }

        $this->close();
        $this->executeCommands($scheduler);

        return $result;
    }

    protected function login()
    {
        $this->ftp = ftp_connect($this->connection['host']);
        if(! $this->ftp) {
            return 'Error: FTP connection to '.$this->connection['host'].' failed';
        }

        if($this->connection['username']) {
            $login = ftp_login($this->ftp, $this->connection['username'], $this->connection['password']);
            if(! $login) {
                return 'Error: FTP login for '.$this->connection['username'].' to '.$this->connection['host'].' failed';
            }
        }

        return null;
    }

    protected function upload($file)
    {
        $success = ftp_put(
            $this->ftp,
            $this->connection['path'].DIRECTORY_SEPARATOR.$file,
            $this->getRoot().DIRECTORY_SEPARATOR.$file,
            FTP_BINARY
        );

        if($success) {
            return '+ '.$file;
        }

        return 'Error: FTP upload failed for '.$file;
    }

    protected function close()
    {
        ftp_close($this->ftp);
    }
}