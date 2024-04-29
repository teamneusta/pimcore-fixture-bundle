<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class AllFixturesLocator implements FixtureLocatorInterface
{
    /**
     * @param \Traversable<FixtureInterface> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
    ) {
    }

    public function getFixtures(): array
    {
        return iterator_to_array($this->allFixtures);
    }
}
