<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixture;
use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

class MockFixtureTwoDependsOnOne implements Fixture, DependentFixture
{
    public function create(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [
            MockFixtureOneDependsOnTwo::class,
        ];
    }
}
