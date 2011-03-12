<?php

namespace Bundle\DeploymentBundle\Deployer;

class Config
{
    protected $rules;
    protected $commands;
    protected $servers;
    protected $config;

    public function __construct(array $rules, array $commands, array $servers)
    {
        $this->rules = $rules;
        $this->commands = $commands;
        $this->servers = $servers;
        $this->config = $config;
    }

    public function getServerNames()
    {
        return array_keys($this->servers);
    }

    public function getSeverConfig($name)
    {
        if (!isset($this->servers[$name])) {
            throw new \InvalidArgumentException(sprintf('Server "%s" not configured', $name));
        }

        if (!isset($this->config[$name])) {
            $this->config[$name] = array(
                'connection' => $this->getConnectionConfig($this->server[$name]),
                'rules' => $this->getRulesConfig($this->server[$name]),
                'commands' => $this->getCommandsConfig($this->server[$name]),
            );
        }

        return $this->config[$name];
    }

    protected function getConnectionConfig($server)
    {
        $config = $this->server[$server];
        unset($config['rules'], $config['commands']);

        return $config;
    }

    protected function getRulesConfig($server)
    {
        $config = array(
        	'ignore' => array(),
        	'force' => array()
        );

        $parameters = array_keys($config);

        foreach ($this->server[$server]['rules'] as $name) {
            if (!isset($this->rules[$name])) {
                throw new \InvalidArgumentException(sprintf('Rule "%s" declared in server "%s" is not configured', $name, $server));
            }

            foreach ($parameters as $parameter) {
                $config[$parameter] = array_merge($config[$parameter], $this->rules[$name][$parameter]);
            }
        }

        return $config;
    }

    protected function getCommandsConfig($server)
    {
        $config = array();

        foreach ($this->server[$server]['commands'] as $name) {
            if (!isset($this->commands[$name])) {
                throw new \InvalidArgumentException(sprintf('Command "%s" declared in server "%s" is not configured', $name, $server));
            }

            $config[] = $this->commands[$name];
        }

        return $config;
    }
}