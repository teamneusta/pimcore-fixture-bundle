<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Profiler\FixtureReference\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

class MockFixtureWithoutDependencies implements Fixture, HasDependencies
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
