<?php

namespace BeSimple\DeploymentBundle\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Server
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $decorator;

    /**
     * @var ArrayCollection
     */
    protected $versions;

    /**
     * @var string|null
     */
    protected $sshAdapter;

    /**
     * @var Identity|null
     */
    protected $sshIdentity;

    /**
     * @var integer
     */
    protected $sshPort;

    /**
     * @var string|null
     */
    protected $deployerAdapter;

    /**
     * @var Identity|null
     */
    protected $deployerIdentity;

    /**
     * @var integer
     */
    protected $deployerPort;

    /**
     * @var array
     */
    protected $deployerConfig;

    public function __construct($name = null)
    {
        $this->name             = $name;
        $this->description      = null;
        $this->host             = null;
        $this->path             = null;
        $this->decorator        = 'None';
        $this->versions         = new ArrayCollection();
        $this->sshAdapter       = null;
        $this->sshIdentity      = null;
        $this->sshPort          = 22;
        $this->deployerAdapter  = null;
        $this->deployerIdentity = null;
        $this->deployerPort     = null;
        $this->deployerConfig   = array();
    }

    /**
     * @param string $decorator
     */
    public function setDecorator($decorator)
    {
        $this->decorator = $decorator;
    }

    /**
     * @return string
     */
    public function getDecorator()
    {
        return $this->decorator;
    }

    /**
     * @param null|string $deployerAdapter
     */
    public function setDeployerAdapter($deployerAdapter)
    {
        $this->deployerAdapter = $deployerAdapter;
    }

    /**
     * @return null|string
     */
    public function getDeployerAdapter()
    {
        return $this->deployerAdapter;
    }

    /**
     * @param array $deployerConfig
     */
    public function setDeployerConfig(array $deployerConfig)
    {
        $this->deployerConfig = $deployerConfig;
    }

    /**
     * @return array
     */
    public function getDeployerConfig()
    {
        return $this->deployerConfig;
    }

    /**
     * @param Identity|null $deployerIdentity
     */
    public function setDeployerIdentity($deployerIdentity)
    {
        $this->deployerIdentity = $deployerIdentity;
    }

    /**
     * @return Identity|null
     */
    public function getDeployerIdentity()
    {
        return $this->deployerIdentity;
    }

    /**
     * @param int $deployerPort
     */
    public function setDeployerPort($deployerPort)
    {
        $this->deployerPort = $deployerPort;
    }

    /**
     * @return int
     */
    public function getDeployerPort()
    {
        return $this->deployerPort;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param null|string $sshAdapter
     */
    public function setSshAdapter($sshAdapter)
    {
        $this->sshAdapter = $sshAdapter;
    }

    /**
     * @return null|string
     */
    public function getSshAdapter()
    {
        return $this->sshAdapter;
    }

    /**
     * @param Identity|null $sshIdentity
     */
    public function setSshIdentity($sshIdentity)
    {
        $this->sshIdentity = $sshIdentity;
    }

    /**
     * @return Identity|null
     */
    public function getSshIdentity()
    {
        return $this->sshIdentity;
    }

    /**
     * @param int $sshPort
     */
    public function setSshPort($sshPort)
    {
        $this->sshPort = $sshPort;
    }

    /**
     * @return int
     */
    public function getSshPort()
    {
        return $this->sshPort;
    }

    /**
     * @return ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }
}
