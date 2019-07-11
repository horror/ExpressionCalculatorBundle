<?php

namespace horror\ExpressionCalculatorBundle\DependencyInjection;

use horror\ExpressionCalculatorBundle\Adapters\AdapterInterface;
use horror\ExpressionCalculatorBundle\Adapters\DefaultAdapter;
use horror\ExpressionCalculatorBundle\ExpressionCalculator;
use http\Exception\RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ExpressionCalculatorExtension.
 */
class ExpressionCalculatorExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $calcDefinition = new Definition(ExpressionCalculator::class);

        $adapterClassName = $config['adapter_class'];

        $interfaces = class_implements($adapterClassName);
        if(!is_array($interfaces) || !in_array(AdapterInterface::class, $interfaces, true)) {
            throw new \RuntimeException(
                "Adapter `$adapterClassName` in config `expression_calculator.adapter_class` must implement interface `horror\ExpressionCalculatorBundle\Adapters\AdapterInterface`"
            );
        }

        $adapter = new $adapterClassName();

        $calcDefinition->replaceArgument(0, $adapter);

        $container->setDefinition(ExpressionCalculator::class, $calcDefinition);
    }
}
