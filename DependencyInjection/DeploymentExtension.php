<?php

namespace Bundle\DeploymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bundle\DeploymentBundle\Service\Config;

class DeploymentExtension extends Extension
{

    public function serversLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('deployment')) {
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
            $loader->load('deployment.xml');
        }

        $servers = array();

        foreach($config as $name => $server) {
            $servers[$name] = array(
                'type' => (isset($server['type']) && is_string($server['type'])) ? $server['type'] : null,
                'rules' => (isset($server['rules']) && is_array($server['rules'])) ? $server['rules'] : null,
                'rule' => array(),
                'connection' => array(),
            );

            foreach(Config::getConnectionParameters() as $parameter) {
                $servers[$name]['connection'][$parameter] = $server[$parameter];
            }

            foreach(Config::getRuleParameters() as $parameter) {
                $servers[$name]['rule'][$parameter] = $server[$parameter];
            }
        }

        $container->setParameter('deployment.servers', $servers);
    }

    public function rulesLoad($config, ContainerBuilder $container)
    {
        $rules = array();

        foreach($container->getParameter('deployment.rules') as $name => $rule) {
            $rules[$name] = array(
                'ignore' => isset($rule['ignore']) ? $this->stringToArray($rule['ignore']) : array(),
                'force' => isset($rule['force']) ? $this->stringToArray($rule['force']) : array(),
                'commands' => isset($rule['commands']) ? $rule['commands'] : array(),
            );
        }

        $container->setParameter('deployment.rules', $rules);
    }

    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 'deployment';
    }

    protected function stringToArray($string)
    {
        $array = array();

        foreach(explode(',', $string) as $bit) {
            $array[] = trim($bit);
        }

        return $array;
    }
}