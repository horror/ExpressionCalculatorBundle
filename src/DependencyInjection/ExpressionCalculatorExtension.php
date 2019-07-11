<?php

namespace horror\ExpressionCalculatorBundle\DependencyInjection;

use horror\ExpressionCalculatorBundle\Adapters\AdapterInterface;
use horror\ExpressionCalculatorBundle\ExpressionCalculator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $calcDefinition = new Definition(ExpressionCalculator::class);

        $adapterClassName = $config['adapter_class'];

        $interfaces = class_implements($adapterClassName);
        if (!is_array($interfaces) || !in_array(AdapterInterface::class, $interfaces, true)) {
            throw new \RuntimeException(
                "Adapter `$adapterClassName` in config `expression_calculator.adapter_class` must implement interface `horror\ExpressionCalculatorBundle\Adapters\AdapterInterface`"
            );
        }

        $calcDefinition->addArgument(new Reference($adapterClassName));
        $calcDefinition->setPublic(true);

        $container->setDefinition(ExpressionCalculator::class, $calcDefinition);
    }
}
