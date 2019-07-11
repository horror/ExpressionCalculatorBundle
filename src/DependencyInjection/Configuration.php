<?php

namespace horror\ExpressionCalculatorBundle\DependencyInjection;

use horror\ExpressionCalculatorBundle\Adapters\DefaultAdapter;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('expression_calculator');
        $root = $treeBuilder->getRootNode();
        $root
            ->children()
                ->scalarNode('adapter_class')->defaultValue(DefaultAdapter::class)
            ->end()
        ;

        return $treeBuilder;
    }
}
