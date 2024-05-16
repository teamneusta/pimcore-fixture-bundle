<?php

namespace Neusta\Pimcore\FixtureBundle\Profiler\PerformanceInfo;

class PerformanceInfo
{
    public function __construct(
        public readonly float $duration,
        public readonly int $memory,
    ) {
    }
}
