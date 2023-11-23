<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Factory\FixtureInstantiator;

use NspPimcore\FixtureBase\Fixture;

interface FixtureInstantiator
{
    public function supports(string $fixtureClass): bool;

    public function instantiate(string $fixtureClass): Fixture;
}
