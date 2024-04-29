<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

interface FixtureLocatorInterface
{
    /**
     * @return array<FixtureInterface>
     */
    public function getFixtures(): array;
}
