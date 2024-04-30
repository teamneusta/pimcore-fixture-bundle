<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class BeforeLoadFixtures
{
    /**
     * @param list<FixtureInterface> $fixtures
     */
    public function __construct(
        private array $fixtures,
    ) {
    }

    /**
     * @return list<FixtureInterface>
     */
    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    /**
     * @param list<FixtureInterface> $fixtures
     */
    public function setFixtures(array $fixtures): void
    {
        $this->fixtures = $fixtures;
    }
}
