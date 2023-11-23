<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator;

use Neusta\Pimcore\FixtureBundle\Fixture;

interface FixtureInstantiator
{
    public function supports(string $fixtureClass): bool;

    public function instantiate(string $fixtureClass): Fixture;
}
