<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('neusta_pimcore_fixture');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('asset_base_path')->defaultValue(null)->end()
                ->scalarNode('data_object_base_path')->defaultValue(null)->end()
                ->scalarNode('document_base_path')->defaultValue(null)->end()
            ->end();

        return $treeBuilder;
    }
}
