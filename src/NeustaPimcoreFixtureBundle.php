<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle;

use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NeustaPimcoreFixtureBundle extends Bundle implements DependentBundleInterface
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new DoctrineFixturesBundle());
    }
}
