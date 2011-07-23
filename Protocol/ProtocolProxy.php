<?php

namespace BeSimple\DeploymentBundle\Protocol;

use BeSimple\DeploymentBundle\Protocol\Shell\ShellInterface;
use BeSimple\DeploymentBundle\Protocol\Files\FilesInterface;
use BeSimple\DeploymentBundle\Protocol\Versioning\VersioningInterface;
use BeSimple\DeploymentBundle\Protocol\Exceptions\AvailabilityException;
use BeSimple\DeploymentBundle\Model\Commands;
use BeSimple\DeploymentBundle\Model\Transfert;
use BeSimple\DeploymentBundle\Model\Server;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class ProtocolProxy
{
    protected $server;
    protected $shell;
    protected $files;
    protected $versioning;

    /**
     * @param Server                   $server
     * @param ShellInterface           $shell
     * @param FilesInterface|null      $files
     * @param VersioningInterface|null $versioning
     */
    public function __construct(Server $server, ShellInterface $shell, FilesInterface $files = null, VersioningInterface $versioning = null)
    {
        $this->server     = $server;
        $this->shell      = $shell;
        $this->files      = $files;
        $this->versioning = $versioning;
    }

    /**
     * @param  Commands $commands
     * @return boolean
     */
    public function execute(Commands $commands)
    {
        return $this->shell->execute($this->server, $commands);
    }

    /**
     * @param  Deployment $deployment
     * @return boolean
     * @throw  AvailabilityException
     */
    public function transfert(Transfert $transfert)
    {
        if (is_null($this->files)) {
            throw new AvailabilityException('Files transfert', $this->server);
        }

        return $this->files->transfert($this->server, $transfert);
    }

    /**
     * @param  string  $source
     * @return boolean
     * @throw  AvailabilityException
     */
    public function checkout($source)
    {
        if (is_null($this->versioning)) {
            throw new AvailabilityException('Versioning', $this->server);
        }

        return $this->versioning->checkout($this->server, $source);
    }
}
