<?php

namespace BeSimple\DeploymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->root('be_simple_deployment', 'array');

        $this->addRsyncSection($rootNode);
        $this->addSshSection($rootNode);
        $this->addRulesSection($rootNode);
        $this->addCommandsSection($rootNode);
        $this->addServersSection($rootNode);

        return $treeBuilder->buildTree();
    }

    protected function addRsyncSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('rsync')->children()
                ->scalarNode('command')->defaultValue('rsync')->cannotBeEmpty()->end()
                ->booleanNode('delete')->defaultFalse()->end()
                ->scalarNode('options')->defaultValue('-Cva')->end()
                ->scalarNode('root')->defaultValue('%kernel.root_dir%/..')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    protected function addSshSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('rsync')->children()
                ->scalarNode('pubkey_file')->defaultNull()->end()
                ->scalarNode('privkey_file')->defaultNull()->end()
                ->scalarNode('passpharse')->defaultNull()->end()
            ->end()
        ;
    }

    protected function addRulesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('rules')
                ->useAttributeAsKey('name')
                ->prototype('array')->children()
                    ->arrayNode('ignore')->defaultValue(array())->end()
                    ->arrayNode('force')->defaultValue(array())->end()
                ->end()
            ->end()
        ;
    }

    protected function addCommandsSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('commands')
            ->useAttributeAsKey('name')
            ->children()
                ->scalarNode('command')->defaultValue('./app/console')->cannotBeEmpty()->end()
            ->end()
            ->prototype('array')->children()
                ->arrayNode('type')->defaultValue('symfony')->end()
                ->arrayNode('command')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('env')->defaultNull()->end()
            ->end()
        ;
    }

    protected function addServersSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('servers')
                ->useAttributeAsKey('name')
                ->prototype('array')->children()
                    ->scalarNode('host')->defaultValue('localhost')->end()
                    ->scalarNode('rsync_port')->defaultNull()->end()
                    ->scalarNode('ssh_port')->defaultValue(22)->cannotBeEmpty()->end()
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
