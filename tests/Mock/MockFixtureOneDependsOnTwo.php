<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class MockFixtureOneDependsOnTwo implements FixtureInterface, DependentFixtureInterface
{
    public function create(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [
            MockFixtureTwoDependsOnOne::class,
        ];
    }
}
