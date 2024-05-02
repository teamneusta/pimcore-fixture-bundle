<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class BeforeExecuteFixture
{
    public function __construct(
        public readonly Fixture $fixture,
        public bool $preventExecution = false,
    ) {
    }
}
