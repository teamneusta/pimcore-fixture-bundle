<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class AfterExecuteFixture
{
    public function __construct(
        public readonly Fixture $fixture,
    ) {
    }
}
