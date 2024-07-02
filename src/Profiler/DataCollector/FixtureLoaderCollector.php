<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\DataCollector;

use Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference\FixtureReference;
use Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference\FixtureReferenceResolver;
use Neusta\Pimcore\FixtureBundle\Profiler\PerformanceInfo\PerformanceInfo;
use Neusta\Pimcore\FixtureBundle\Profiler\Timing\TimingCollector;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class FixtureLoaderCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly FixtureReferenceResolver $fixtureReferenceResolver,
        private readonly TimingCollector $timingCollector,
    ) {
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data['timings'] = $this->timingCollector->getAll();

        $this->data['references'] = $this->fixtureReferenceResolver->getRootReferences();
    }

    public static function getTemplate(): ?string
    {
        return '@NeustaPimcoreFixture/data_collector/template.html.twig';
    }

    /**
     * @return list<FixtureReference>
     */
    public function getDependencyFreeFixtures(): array
    {
        return $this->data['references'];
    }

    public function getTiming(FixtureReference $fixtureReference): PerformanceInfo
    {
        return $this->data['timings'][$fixtureReference->getName()];
    }

    /**
     * @return array<class-string, PerformanceInfo>
     */
    public function getTimings(): array
    {
        return $this->data['timings'];
    }
}
