<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

interface FixtureLocator
{
    /**
     * @return list<Fixture>
     */
    public function getFixtures(): array;
}
