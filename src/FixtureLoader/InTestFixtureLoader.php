<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\FixtureLoader;

use Neusta\Pimcore\FixtureBundle\Executor\ExecutorInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;
use Neusta\Pimcore\FixtureBundle\Locator\NamedFixtureLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InTestFixtureLoader extends FixtureLoader
{
    public function __construct(
        private readonly NamedFixtureLocator $fixtureLocator,
        ExecutorInterface $executor,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($fixtureLocator, $executor, $eventDispatcher);
    }

    /**
     * @param list<class-string<FixtureInterface>> $fixtureNames
     *
     * @return $this
     */
    public function setFixturesToLoad(array $fixtureNames): self
    {
        $this->fixtureLocator->setFixturesToLoad($fixtureNames);

        return $this;
    }
}
