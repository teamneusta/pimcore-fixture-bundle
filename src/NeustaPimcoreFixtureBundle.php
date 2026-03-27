<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle;

use Neusta\Pimcore\FixtureBundle\DependencyInjection\Compiler\RemoveLoggingMiddlewarePass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NeustaPimcoreFixtureBundle extends AbstractPimcoreBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveLoggingMiddlewarePass());
    }
}
