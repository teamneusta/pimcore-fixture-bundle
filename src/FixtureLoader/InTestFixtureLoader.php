<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\FixtureLoader;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;
use Neusta\Pimcore\FixtureBundle\Locator\NamedFixtureLocator;

class InTestFixtureLoader extends FixtureLoader
{
    /**
     * @param list<class-string<FixtureInterface>> $fixtureNames
     *
     * @return $this
     */
    public function setFixturesToLoad(array $fixtureNames): self
    {
        if (!$this->fixtureLocator instanceof NamedFixtureLocator) {
            throw new \UnexpectedValueException();
        }

        $this->fixtureLocator->setFixturesToLoad($fixtureNames);

        return $this;
    }
}
