<?php

namespace BeSimple\DeploymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('besimple_deployment', 'array');

        $this->addRsyncSection($rootNode);
        $this->addSshSection($rootNode);
        $this->addRulesSection($rootNode);
        $this->addCommandsSection($rootNode);
        $this->addServersSection($rootNode);

        return $treeBuilder->buildTree();
    }

    protected function addRsyncSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('rsync')
                ->scalarNode('command')->defaultValue('rsync')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('delete')->defaultFalse()->end()
                ->scalarNode('options')->defaultValue('-Cva')->end()
                ->scalarNode('root')->defaultValue('%kernel.root_dir%/..')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    protected function addSshSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('rsync')
                ->scalarNode('pubkey_file')->defaultNull()->end()
                ->scalarNode('privkey_file')->defaultNull()->end()
                ->scalarNode('passpharse')->defaultNull()->end()
            ->end()
        ;
    }

    protected function addRulesSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('rules')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->arrayNode('ignore')->defaultValue(array())->end()
                    ->arrayNode('force')->defaultValue(array())->end()
                ->end()
            ->end()
        ;
    }

    protected function addCommandsSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('commands')
                ->scalarNode('command')->defaultValue('./app/console')->isRequired()->cannotBeEmpty()->end()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->arrayNode('type')->defaultValue('symfony')->end()
                    ->arrayNode('command')->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode('env')->defaultNull()->end()
                ->end()
            ->end()
        ;
    }

    protected function addServersSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('servers')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->scalarNode('host')->defaultValue('localhost')->end()
                    ->scalarNode('rsync_port')->defaultNull()->end()
                    ->scalarNode('ssh_port')->defaultNull()->end()
                    ->scalarNode('username')->defaultNull()->end()
                    ->scalarNode('password')->defaultNull()->end()
                    ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode('rules')->defaultValue(array())->end()
                    ->arrayNode('commands')->defaultValue(array())->end()
                ->end()
            ->end()
        ;
    }
}