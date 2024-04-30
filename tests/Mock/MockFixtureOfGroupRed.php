<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureGroupInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class MockFixtureOfGroupRed implements FixtureInterface, FixtureGroupInterface
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
