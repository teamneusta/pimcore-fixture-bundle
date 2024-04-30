<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\FixtureLoader;

use Neusta\Pimcore\FixtureBundle\Event\AfterExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Event\AfterLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Event\BeforeExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Event\BeforeLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Executor\ExecutorInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;
use Neusta\Pimcore\FixtureBundle\Locator\FixtureLocatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FixtureLoader
{
    public function __construct(
        protected readonly FixtureLocatorInterface $fixtureLocator,
        private readonly ExecutorInterface $executor,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function loadFixtures(): void
    {
        $locatedFixtures = $this->locateFixtures();
        /** @var BeforeLoadFixtures $beforeLoadFixtures */
        $beforeLoadFixtures = $this->eventDispatcher->dispatch(new BeforeLoadFixtures($locatedFixtures));
        $fixtures = $beforeLoadFixtures->getFixtures();

        $loadedFixtures = [];
        foreach ($fixtures as $fixture) {
            /** @var BeforeExecuteFixture $beforeExecuteFixture */
            $beforeExecuteFixture = $this->eventDispatcher->dispatch(new BeforeExecuteFixture($fixture));
            if ($beforeExecuteFixture->preventExecution()) {
                continue;
            }

            $this->executor->execute($fixture);
            $this->eventDispatcher->dispatch(new AfterExecuteFixture($fixture));

            $loadedFixtures[] = $fixture;
        }

        $this->eventDispatcher->dispatch(new AfterLoadFixtures($loadedFixtures));
    }

    /**
     * @return array<FixtureInterface>
     */
    protected function locateFixtures(): array
    {
        return $this->fixtureLocator->getFixtures();
    }
}
