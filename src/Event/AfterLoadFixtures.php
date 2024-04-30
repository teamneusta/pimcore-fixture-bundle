<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class AfterLoadFixtures
{
    /**
     * @param list<Fixture> $loadedFixtures
     */
    public function __construct(
        public readonly array $loadedFixtures,
    ) {
    }
}
