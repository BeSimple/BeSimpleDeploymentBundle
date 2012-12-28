<?php

namespace BeSimple\DeploymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class BeSimpleDeploymentExtension extends Extension
{
    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('deployment.xml');

        $container->setParameter('be_simple_deployment.config.rsync', isset($config['rsync']) ? $config['rsync'] : array());
        $container->setParameter('be_simple_deployment.config.ssh', isset($config['ssh']) ? $config['ssh'] : array());
        $container->setParameter('be_simple_deployment.config.rules', isset($config['rules']) ? $config['rules'] : array());
        $container->setParameter('be_simple_deployment.config.commands', isset($config['commands']) ? $config['commands'] : array());
        $container->setParameter('be_simple_deployment.config.servers', isset($config['servers']) ? $config['servers'] : array());
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'be_simple_deployment';
    }
}
