<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasGroups;

class MockFixtureOfGroupRed implements Fixture, HasGroups
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
