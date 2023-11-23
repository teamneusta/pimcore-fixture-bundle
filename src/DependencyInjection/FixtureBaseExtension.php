<?php
declare(strict_types=1);

namespace NspPimcore\FixtureBase\DependencyInjection;

use NspPimcore\FixtureBase\Command\LoadFixturesCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class FixtureBaseExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $definition = $container->getDefinition(LoadFixturesCommand::class);
        $definition->setArgument('$fixtureClass', $mergedConfig['fixture_class']);
    }
}
