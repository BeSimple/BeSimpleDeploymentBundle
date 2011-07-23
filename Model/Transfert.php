<?php

namespace BeSimple\DeploymentBundle\Model;

use Symfony\Component\Finder\Finder;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Transfert
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var array
     */
    protected $ignore;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
        $this->ignore  = array();
    }

    /**
     * @return Finder
     */
    public function getFinder(Finder $finder = null)
    {
        $finder = $finder ?: new Finder();

        foreach ($this->ignore as $pattern) {
            $finder->notName($pattern);
        }

        return $finder->in($this->rootDir);
    }

    /**
     * @param string $pattern
     */
    public function addIgnore($pattern)
    {
        $this->ignore[] = $pattern;
    }

    /**
     * @param array $ignore
     */
    public function setIgnore(array $ignore)
    {
        $this->ignore = $ignore;
    }

    /**
     * @return array
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }
}
