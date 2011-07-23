<?php

namespace BeSimple\DeploymentBundle\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Server
{
    const DEPLOYMENT_METHOD_FILES      = 1;
    const DEPLOYMENT_METHOD_VERSIONING = 2;

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
    protected $shellAdapter;

    /**
     * @var Identity|null
     */
    protected $shellIdentity;

    /**
     * @var integer
     */
    protected $shellPort;

    /**
     * @var string|null
     */
    protected $filesAdapter;

    /**
     * @var Identity|null
     */
    protected $filesIdentity;

    /**
     * @var integer
     */
    protected $filesPort;

    /**
     * @var string
     */
    protected $versioningSource;

    /**
     * @var integer
     */
    protected $deploymentMethod;

    public function __construct($name = null)
    {
        $this->name             = $name;
        $this->description      = null;
        $this->host             = null;
        $this->path             = null;
        $this->decorator        = 'None';
        $this->versions         = new ArrayCollection();
        $this->shellAdapter     = null;
        $this->shellIdentity    = null;
        $this->shellPort        = null;
        $this->filesAdapter     = null;
        $this->filesIdentity    = null;
        $this->filesPort        = null;
        $this->versioningSource = array();
        $this->deploymentMethod = self::DEPLOYMENT_METHOD_FILES;
    }

}
