<?php

namespace BeSimple\DeploymentBundle\Model;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Commands implements \Iterator, \Countable
{
    /**
     * @var string
     */
    protected $symfonyPattern;

    /**
     * @var array
     */
    protected $commands;

    /**
     * @var integer
     */
    protected $cursor;

    /**
     * @param string $symfonyPattern
     */
    public function __construct($symfonyPattern = './app/console %s')
    {
        $this->symfonyPattern = $symfonyPattern;
        $this->commands       = array();
        $this->cursor         = 0;
    }

    /**
     * @return string
     */
    public function current()
    {
        return $this->commands[$this->cursor]->getCommandLine($this->symfonyPattern);
    }

    /**
     * @param  Command $command
     * @return void
     */
    public function push(Command $command)
    {
        $this->commands[] = $command;
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->commands);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->cursor = 0;
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->cursor ++;
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->cursor;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return isset($this->commands[$this->cursor]);
    }
}
