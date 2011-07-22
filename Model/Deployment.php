<?php

namespace BeSimple\DeploymentBundle\Model;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Deployment
{
    const STATUS_UNKNOWN   = 0;
    const STATUS_STARTED   = 1;
    const STATUS_SUCCESS   = 2;
    const STATUS_ERROR     = 3;
    const STATUS_SCHEDULED = 4;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime
     */
    protected $deployedAt;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string|null
     */
    protected $logs;

    public function __construct($message = null)
    {
        $this->message    = $message;
        $this->deployedAt = null;
        $this->status     = self::STATUS_UNKNOWN;
        $this->directory  = null;
        $this->logs       = null;
    }

    /**
     * @param \DateTime $deployedAt
     */
    public function setDeployedAt($deployedAt)
    {
        $this->deployedAt = $deployedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeployedAt()
    {
        return $this->deployedAt;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param null|string $logs
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

    /**
     * @return null|string
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
