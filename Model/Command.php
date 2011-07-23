<?php

namespace BeSimple\DeploymentBundle\Model;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Command
{
    const TYPE_SHELL   = 1;
    const TYPE_SYMFONY = 2;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var string
     */
    protected $command;

    /**
     * @param int         $type
     * @param string|null $command
     */
    public function __contsruct($type = self::TYPE_SHELL, $command = null)
    {
        $this->type    = $type;
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommandLine($symfonyPattern)
    {
        if ($this->type === self::TYPE_SYMFONY) {
            return sprintf($symfonyPattern, $this->command);
        }

        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $symfony
     */
    public function setSymfony($symfony)
    {
        $this->symfony = $symfony;
    }

    /**
     * @return string
     */
    public function getSymfony()
    {
        return $this->symfony;
    }

    /**
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}
