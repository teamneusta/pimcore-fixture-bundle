<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class MockFixtureWithoutDependencies implements FixtureInterface
{
    public function load(): void
    {
        // no-op
    }
}
