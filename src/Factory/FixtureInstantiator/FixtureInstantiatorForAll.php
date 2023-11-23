<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Factory\FixtureInstantiator;

use NspPimcore\FixtureBase\Fixture;

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
