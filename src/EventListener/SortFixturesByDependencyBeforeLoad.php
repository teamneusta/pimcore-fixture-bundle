<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\BeforeLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Sorter\FixtureDependencySorter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SortFixturesByDependencyBeforeLoad implements EventSubscriberInterface
{
    public function __construct(
        private readonly FixtureDependencySorter $dependencySorter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLoadFixtures::class => 'sortFixturesByDependency',
        ];
    }

    public function sortFixturesByDependency(BeforeLoadFixtures $event): void
    {
        $event->fixtures = $this->dependencySorter->sort($event->fixtures);
    }
}
