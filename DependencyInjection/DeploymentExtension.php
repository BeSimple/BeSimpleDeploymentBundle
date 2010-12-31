<?php

namespace Bundle\DeploymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeploymentExtension extends Extension
{

    public function serversLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('deployment')) {
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
            $loader->load('deployment.xml');
        }

        $defaultType = $container->getParameter('deployment.servers.default.type');
        $defaultConnection = $container->getParameter('deployment.servers.default.connexion');

        foreach($config as $server => $config) {
            $container->setParameter('deployment.servers.'.$server.'.type', isset($config['type']) ? $config['type'] : $defaultType);
            $container->setParameter('deployment.server.'.$server.'.connection', array_merge($defaultConnection, $connection));
            $container->setParameter('deployment.server.'.$server.'.connection', $config['rules']);
        }

        $container->setParameter('deployment.servers.list', array_keys($config));
    }

    public function rulesLoad($config, ContainerBuilder $container)
    {
        foreach($config as $rule => $config) {
            $container->setParameter('deployment.rule.'.$rule, $config);
        }

        $container->setParameter('deployment.rules.list', array_keys($config));
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

}