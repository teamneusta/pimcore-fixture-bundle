<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\BeforeLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Sorter\FixtureDependencySorter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SortFixturesByDependencyBeforeLoad implements EventSubscriberInterface
{
    /**
     * @param \Traversable<Fixture> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
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
        $event->fixtures = (new FixtureDependencySorter(iterator_to_array($this->allFixtures, false)))
            ->sort($event->fixtures);
    }
}
