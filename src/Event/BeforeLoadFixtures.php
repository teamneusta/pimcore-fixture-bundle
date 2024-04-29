<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class BeforeLoadFixtures
{
    /**
     * @param array<FixtureInterface> $fixtures
     */
    public function __construct(
        private array $fixtures,
    ) {
    }

    /**
     * @return array<FixtureInterface>
     */
    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    /**
     * @param array<FixtureInterface> $fixtures
     */
    public function setFixtures(array $fixtures): void
    {
        $this->fixtures = $fixtures;
    }
}
