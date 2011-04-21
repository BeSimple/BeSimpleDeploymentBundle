<?php

namespace BeSimple\DeploymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BeSimpleDeploymentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('deployment.xml');

        $container->setParameter('besimple_deployment.config.rsync', $config['rsync']);
        $container->setParameter('besimple_deployment.config.ssh', $config['rsync']);
        $container->setParameter('besimple_deployment.config.rules', $config['rules']);
        $container->setParameter('besimple_deployment.config.commands', $config['commands']);
        $container->setParameter('besimple_deployment.config.servers', $config['servers']);
    }

    public function getAlias()
    {
        return 'besimple_deployment';
    }
}
