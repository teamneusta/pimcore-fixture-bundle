<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;

class MockDependentFixtureWithoutDependencies implements DependentFixtureInterface
{
    public function create(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [];
    }
}
