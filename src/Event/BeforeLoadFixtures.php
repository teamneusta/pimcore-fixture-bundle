<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class BeforeLoadFixtures
{
    /**
     * @param list<FixtureInterface> $fixtures
     */
    public function __construct(
        public array $fixtures,
    ) {
    }
}
