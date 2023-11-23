<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator;

use Neusta\Pimcore\FixtureBundle\Fixture;

class FixtureInstantiatorForAll implements FixtureInstantiator
{
    public function supports(string $fixtureClass): bool
    {
        return true;
    }

    public function instantiate(string $fixtureClass): Fixture
    {
        return new $fixtureClass();
    }
}
