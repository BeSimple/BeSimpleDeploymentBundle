<?php

namespace BeSimple\DeploymentBundle\Protocol;

use BeSimple\DeploymentBundle\Model\Server;
use BeSimple\DeploymentBundle\Model\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class ProtocolFactory
{
    const TYPE_SHELL      = 'shell';
    const TYPE_FILES      = 'files';
    const TYPE_VERSIONING = 'versioning';

    protected $dispatcher;
    protected $rootDir;
    protected $container;

    /**
     * @param EventDispatcher $dispatcher
     * @param string          $rootDir
     * @param Container       $container
     */
    public function __construct(EventDispatcher $dispatcher, $rootDir, Container $container)
    {
        $this->dispatcher = $dispatcher;
        $this->rootDir    = $rootDir;
        $this->container  = $container;
    }

    public function server($name)
    {

    }

    /**
     * @param  string|null    $adapter
     * @return ShellInterface
     */
    public function shell($adapter = null)
    {
        return $this->protocol(self::TYPE_SHELL, $adapter ?: $this->getDefaultShellAdapter());
    }

    /**
     * @param  string|null    $adapter
     * @return FilesInterface
     */
    public function files($adapter = null)
    {
        return $this->protocol(self::TYPE_FILES, $adapter ?: $this->getDefaultFilesAdapter());
    }

    /**
     * @param  string              $adapter
     * @return VersioningInterface
     */
    public function versioning($adapter)
    {
        return $this->protocol(self::TYPE_VERSIONING, $adapter);
    }

    /**
     * @param  string        $serverName
     * @return ProtocolProxy
     */
    public function proxy($serverName)
    {
        $server     = $this->server($serverName);
        $shell      = $this->shell($server->getShellAdapter());
        $files      = $this->files($server->getFilesAdapter());
        $versioning = $this->versioning($server->getVersioningAdapter());

        return new ProtocolProxy($server, $shell, $files, $versioning);
    }

    /**
     * @param  string            $type
     * @param  string            $adapter
     * @return ProtocolInterface
     */
    protected function protocol($type, $adapter)
    {
        $class = $this->container->getParameter(sprintf('be_simple_deployment.%s.%s.class', $type, $adapter));

        return new $class($this->dispatcher, $this->rootDir);
    }

    protected function getDefaultShellAdapter()
    {
        if (function_exists('shell2_connect')) {
            return 'shell2';
        }

        return 'shell_process';
    }

    protected function getDefaultFilesAdapter()
    {
        return 'ftp';
    }
}
