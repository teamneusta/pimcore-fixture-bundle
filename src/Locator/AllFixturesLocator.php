<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class AllFixturesLocator implements FixtureLocator
{
    /**
     * @param \Traversable<Fixture> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
    ) {
    }

    public function getFixtures(): array
    {
        return iterator_to_array($this->allFixtures, false);
    }
}
