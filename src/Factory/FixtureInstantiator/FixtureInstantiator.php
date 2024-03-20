<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator;

use Neusta\Pimcore\FixtureBundle\Fixture;

interface FixtureInstantiator
{
    /**
     * @param class-string<Fixture> $fixtureClass
     */
    public function supports(string $fixtureClass): bool;

    /**
     * @template T of Fixture
     *
     * @param class-string<T> $fixtureClass
     *
     * @return T
     */
    public function instantiate(string $fixtureClass): Fixture;
}
