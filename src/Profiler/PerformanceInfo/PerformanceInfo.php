<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\PerformanceInfo;

/**
 * @internal
 */
final class PerformanceInfo
{
    public function __construct(
        public readonly float $duration,
        public readonly int $memory,
    ) {
    }
}
