<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\FixtureLoader;

use Neusta\Pimcore\FixtureBundle\Executor\Executor;
use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Locator\NamedFixtureLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SelectiveFixtureLoader extends FixtureLoader
{
    public function __construct(
        private readonly NamedFixtureLocator $fixtureLocator,
        Executor $executor,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($fixtureLocator, $executor, $eventDispatcher);
    }

    /**
     * @param list<class-string<Fixture>> $fixtureNames
     *
     * @return $this
     */
    public function setFixturesToLoad(array $fixtureNames): self
    {
        $this->fixtureLocator->setFixturesToLoad($fixtureNames);

        return $this;
    }
}
