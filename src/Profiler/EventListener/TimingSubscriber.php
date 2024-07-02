<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\AfterExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Event\BeforeExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Profiler\Timing\Timing;
use Neusta\Pimcore\FixtureBundle\Profiler\Timing\TimingCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class TimingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Timing $timing,
        private readonly TimingCollector $timingCollector,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeExecuteFixture::class => 'beforeExecuteFixture',
            AfterExecuteFixture::class => 'afterExecuteFixture',
        ];
    }

    public function beforeExecuteFixture(BeforeExecuteFixture $event): void
    {
        $this->timing->beforeExecuteFixture($event->fixture);
    }

    public function afterExecuteFixture(AfterExecuteFixture $event): void
    {
        $this->timing->afterExecuteFixture($event->fixture);

        $this->timingCollector->add($event->fixture, $this->timing->getPerformanceInfo($event->fixture));
    }
}
