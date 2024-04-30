<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class BeforeLoadFixtures
{
    /**
     * @param list<Fixture> $fixtures
     */
    public function __construct(
        public array $fixtures,
    ) {
    }
}
