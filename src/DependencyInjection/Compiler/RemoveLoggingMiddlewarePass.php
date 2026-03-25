<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @internal */
final class RemoveLoggingMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('monolog.logger.doctrine')) {
            return;
        }

        $container->removeDefinition('neusta_pimcore_fixture.dbal.logging_middleware');
    }
}
