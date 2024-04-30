<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureGroup;
use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

class MockFixtureOfGroupRed implements Fixture, FixtureGroup
{
    public function create(): void
    {
        // no-op
    }

    public static function getGroups(): array
    {
        return [
            'red',
        ];
    }
}
