<?php

namespace Heystack\Subsystem\GiftWrapping\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Heystack\Subsystem\Ecommerce\Config
 */
class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gift-wrapping');

        $rootNode
            ->children()
                ->arrayNode('config')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('code')->isRequired()->end()
                            ->scalarNode('message')->defaultValue('')->end()
                            ->floatNode('price')->defaultValue(0)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('config_db')
                    ->children()
                        ->scalarNode('select')->defaultValue('*')->end()
                        ->scalarNode('from')->isRequired()->end()
                        ->scalarNode('where')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
