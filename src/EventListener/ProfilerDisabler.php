<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * This proxy class allows the PimcoreLoadOptimization to defer its dependency on the Profiler.
 * Previously, PimcoreLoadOptimization was instantiated with every request or CLI command because it's an
 * event subscriber and needs to be initialized by the container. Instantiating PimcoreLoadOptimization created
 * a dependency on the Profiler, which subsequently required CoreShop\Bundle\StoreBundle\Collector\StoreCollector.
 * This cascade of dependencies ultimately triggered a call to EntityRepository::findAll(), leading to an exception
 * when the database was not available.
 *
 * By introducing this lazy initialization mechanism, we can prevent unintended errors caused by unnecessary
 * instantiation and reduce the overhead of initializing the full dependency chain.
 *
 * After the update to symfony 6 this might be replaced by a lazy load service dependency
 *
 * @internal
 */
final class ProfilerDisabler
{
    public function __construct(
        private readonly ?Profiler $profiler = null,
    ) {
    }

    public function disable(): void
    {
        $this->profiler?->disable();
    }

    public function enable(): void
    {
        $this->profiler?->enable();
    }
}
