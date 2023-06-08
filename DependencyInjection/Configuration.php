<?php

namespace ZO\Bundle\SupervisorMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {

        $treeBuilder = new TreeBuilder('zo_supervisor_monitor');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                // ->scalarNode('base_view')
                //     ->defaultValue('')
                // ->end()
                // ->scalarNode('base_block')
                //     ->defaultValue('')
                // ->end()
                ->arrayNode('servers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->end()
                            ->scalarNode('port')->end()
                            ->scalarNode('username')->end()
                            ->scalarNode('password')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
