<?php

namespace BeSimple\DeploymentBundle\Deployer;

class Config
{
    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $commands;

    /**
     * @var array
     */
    protected $servers;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $rules
     * @param array $commands
     * @param array $servers
     */
    public function __construct(array $rules, array $commands, array $servers)
    {
        $this->rules = $rules;
        $this->commands = $commands;
        $this->servers = $servers;
        $this->config = array();
    }

    /**
     * @return array
     */
    public function getServerNames()
    {
        return array_keys($this->servers);
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $server
     * @return array
     */
    public function getServerConfig($server)
    {
        if (!isset($this->servers[$server])) {
            throw new \InvalidArgumentException(sprintf('Server "%s" not configured', $server));
        }

        if (!isset($this->config[$server])) {
            $this->config[$server] = array(
                'connection' => $this->getConnectionConfig($server),
                'rules' => $this->getRulesConfig($server),
                'commands' => $this->getCommandsConfig($server),
            );
        }

        return $this->config[$server];
    }

    /**
     * @param string $server
     * @return array
     */
    protected function getConnectionConfig($server)
    {
        $config = $this->servers[$server];
        unset($config['rules'], $config['commands']);

        return $config;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $server
     * @return array
     */
    protected function getRulesConfig($server)
    {
        $config = array(
        	'ignore' => array(),
        	'force' => array()
        );

        $parameters = array_keys($config);

        foreach ($this->servers[$server]['rules'] as $name) {
            if (!isset($this->rules[$name])) {
                throw new \InvalidArgumentException(sprintf('Rule "%s" declared in server "%s" is not configured', $name, $server));
            }

            foreach ($parameters as $parameter) {
                $config[$parameter] = array_merge($config[$parameter], $this->rules[$name][$parameter]);
            }
        }

        return $config;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $server
     * @return array
     */
    protected function getCommandsConfig($server)
    {
        $config = array();

        foreach ($this->servers[$server]['commands'] as $name) {
            if (!isset($this->commands[$name])) {
                throw new \InvalidArgumentException(sprintf('Command "%s" declared in server "%s" is not configured', $name, $server));
            }

            $config[] = $this->commands[$name];
        }

        return $config;
    }
}
