<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\DependencyInjection;

use Neusta\Pimcore\FixtureBundle\Command\LoadFixturesCommand;
use Neusta\Pimcore\FixtureBundle\Helper\AssetHelper;
use Neusta\Pimcore\FixtureBundle\Helper\DataObjectHelper;
use Neusta\Pimcore\FixtureBundle\Helper\DocumentHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class NeustaPimcoreFixtureExtension extends ConfigurableExtension
{
    /**
     * @param array<string, mixed> $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__, 2) . '/config'));
        $loader->load('services.yaml');

        $definition = $container->getDefinition(LoadFixturesCommand::class);
        $definition->setArgument('$fixtureClass', $mergedConfig['fixture_class']);

        $definition = $container->getDefinition(AssetHelper::class);
        $definition->setArgument('$prefix', $mergedConfig['asset_base_path']);

        $definition = $container->getDefinition(DataObjectHelper::class);
        $definition->setArgument('$prefix', $mergedConfig['data_object_base_path']);

        $definition = $container->getDefinition(DocumentHelper::class);
        $definition->setArgument('$prefix', $mergedConfig['document_base_path']);
    }
}
