<?php

namespace Neusta\Pimcore\FixtureBundle\Profiler\Timing;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Profiler\PerformanceInfo\PerformanceInfo;

class TimingCollector
{
    /** @var array<class-string, PerformanceInfo> */
    private array $timings = [];

    public function add(Fixture $fixture, PerformanceInfo $performanceInfo): void
    {
        $this->timings[get_class($fixture)] = $performanceInfo;
    }

    /**
     * @return array<class-string, PerformanceInfo>
     */
    public function getAll(): array
    {
        return $this->timings;
    }
}
