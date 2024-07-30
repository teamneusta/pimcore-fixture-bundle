<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\Timing;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Profiler\PerformanceInfo\PerformanceInfo;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @internal
 */
final class Timing
{
    private const STOPWATCH_NAME_PREFIX = 'fixture:';
    private const STOPWATCH_CATEGORY = 'execute';

    public function __construct(
        private readonly Stopwatch $stopwatch,
    ) {
    }

    public function beforeExecuteFixture(Fixture $fixture): void
    {
        $this->stopwatch->start($this->stopWatchName($fixture), self::STOPWATCH_CATEGORY);
    }

    public function afterExecuteFixture(Fixture $fixture): void
    {
        $this->stopwatch->stop($this->stopWatchName($fixture));
    }

    private function stopWatchName(Fixture $fixture): string
    {
        $fixtureName = (new \ReflectionClass($fixture))->getShortName();

        return \sprintf(
            '%s%s',
            self::STOPWATCH_NAME_PREFIX,
            $fixtureName
        );
    }

    public function getPerformanceInfo(Fixture $fixture): PerformanceInfo
    {
        try {
            $event = $this->stopwatch->getEvent($this->stopWatchName($fixture));
        } catch (\LogicException) {
            return new PerformanceInfo(-1, -1);
        }

        return new PerformanceInfo(
            $event->getDuration(),
            $event->getMemory(),
        );
    }
}
