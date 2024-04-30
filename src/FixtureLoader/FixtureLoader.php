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
        private readonly FixtureLocatorInterface $fixtureLocator,
        private readonly ExecutorInterface $executor,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function loadFixtures(): void
    {
        $fixtures = $this->eventDispatcher->dispatch(new BeforeLoadFixtures($this->locateFixtures()))->fixtures;

        $loadedFixtures = [];
        foreach ($fixtures as $fixture) {
            if ($this->eventDispatcher->dispatch(new BeforeExecuteFixture($fixture))->preventExecution) {
                continue;
            }

            $this->executor->execute($fixture);
            $this->eventDispatcher->dispatch(new AfterExecuteFixture($fixture));

            $loadedFixtures[] = $fixture;
        }

        $this->eventDispatcher->dispatch(new AfterLoadFixtures($loadedFixtures));
    }

    /**
     * @return list<FixtureInterface>
     */
    protected function locateFixtures(): array
    {
        return $this->fixtureLocator->getFixtures();
    }
}
