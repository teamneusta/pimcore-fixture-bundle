<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

class MockFixtureWithoutDependencies implements Fixture
{
    public function create(): void
    {
        // no-op
    }
}
