<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

class MockFixtureDependsOnItself implements Fixture, HasDependencies
{
    public function create(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [
            self::class,
        ];
    }
}
