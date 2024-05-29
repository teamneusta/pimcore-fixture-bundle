<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\AfterLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Event\BeforeExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference\FixtureReferenceResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FixtureDependencyGraphSubscriber implements EventSubscriberInterface
{
    private array $loadedFixtures = [];

    public function __construct(
        private readonly FixtureReferenceResolver $fixtureReferenceResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeExecuteFixture::class => ['beforeExecuteFixture', 10],
            AfterLoadFixtures::class => 'afterLoadFixtures',
        ];
    }

    public function beforeExecuteFixture(BeforeExecuteFixture $event): void
    {
        $this->loadedFixtures[] = $event->fixture;
    }

    public function afterLoadFixtures(): void
    {
        $this->fixtureReferenceResolver->setFixtures($this->loadedFixtures);
    }
}
