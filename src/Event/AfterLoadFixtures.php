<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class AfterLoadFixtures
{
    /**
     * @param array<FixtureInterface> $loadedFixtures
     */
    public function __construct(
        public readonly array $loadedFixtures,
    ) {
    }
}
