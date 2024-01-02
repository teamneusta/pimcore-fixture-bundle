<?php declare(strict_types=1);

use Neusta\Pimcore\FixtureBundle\NeustaPimcoreFixtureBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new NeustaPimcoreFixtureBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
