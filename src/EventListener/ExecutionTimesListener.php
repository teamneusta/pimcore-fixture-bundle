<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\CreateFixture;
use Neusta\Pimcore\FixtureBundle\Event\FixtureWasCreated;

final class ExecutionTimesListener
{
    /** @var array<string, float> */
    private array $executionTimes = [];
    private float $currentTime;

    public function onCreateFixture(CreateFixture $event): void
    {
        $this->currentTime = microtime(true);
    }

    public function onFixtureWasCreated(FixtureWasCreated $event): void
    {
        $this->executionTimes[$event->fixture::class] = microtime(true) - $this->currentTime;
    }

    /**
     * @return array<string, float>
     */
    public function getExecutionTimes(): array
    {
        return $this->executionTimes;
    }

    public function getTotalTime(): float
    {
        return array_sum($this->executionTimes);
    }
}
