<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\DependencyInjection;

use Kaliop\ContentDecorator\Model\GenericContent;
use Kaliop\ContentDecorator\Repository\ContentRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kaliop_content_decorator');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('mappings')->info('List of directories containing content decorators, indexed by unique name')
                    ->useAttributeAsKey('name')
                        ->arrayPrototype()
                        ->children()
                            ->scalarNode('namespace')->end()
                            ->scalarNode('dir')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('content_types')->info('List of content types to be decorated, indexed by content type identifier')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('default_class')->info('Default class implementing ContentDecorator abstraction')->defaultValue(GenericContent::class)->end()
                ->scalarNode('default_repository_class')->info('Default repository class used for generating content repository classes')->defaultValue(ContentRepository::class)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
