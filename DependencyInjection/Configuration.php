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

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addRsyncSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('rsync')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('command')->defaultValue('rsync')->cannotBeEmpty()->end()
                    ->booleanNode('delete')->defaultFalse()->end()
                    ->scalarNode('options')->defaultValue('-Cva')->end()
                    ->scalarNode('root')->defaultValue('%kernel.root_dir%/..')->cannotBeEmpty()->end()
                ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addSshSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('ssh')->children()
                ->scalarNode('pubkey_file')->defaultNull()->end()
                ->scalarNode('privkey_file')->defaultNull()->end()
                ->scalarNode('passphrase')->defaultNull()->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addRulesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('rules')
                ->useAttributeAsKey('name')
                ->prototype('array')->children()
                    ->arrayNode('ignore')->defaultValue(array())->ignoreExtraKeys()->end()
                    ->arrayNode('force')->defaultValue(array())->ignoreExtraKeys()->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addCommandsSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('commands')
            ->useAttributeAsKey('name')
            ->children()
                ->scalarNode('command')->defaultValue('./app/console')->cannotBeEmpty()->end()
            ->end()
            ->prototype('array')->children()
                ->scalarNode('type')->defaultValue('symfony')->end()
                ->scalarNode('command')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('env')->defaultNull()->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
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
                    ->arrayNode('rules')->defaultValue(array())->ignoreExtraKeys()->end()
                    ->arrayNode('commands')->defaultValue(array())->ignoreExtraKeys()->end()
                ->end()
            ->end()
        ;
    }
}
