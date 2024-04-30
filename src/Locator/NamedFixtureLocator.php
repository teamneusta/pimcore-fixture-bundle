<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class NamedFixtureLocator implements FixtureLocator
{
    /** @var list<class-string<Fixture>> */
    private array $fixturesToLoad = [];

    /**
     * @param \Traversable<Fixture> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
    ) {
    }

    /**
     * @return list<class-string<Fixture>>
     */
    public function getFixturesToLoad(): array
    {
        return $this->fixturesToLoad;
    }

    /**
     * @param list<class-string<Fixture>> $fixtureNames
     *
     * @return $this
     */
    public function setFixturesToLoad(array $fixtureNames): self
    {
        $this->fixturesToLoad = $fixtureNames;

        return $this;
    }

    public function getFixtures(): array
    {
        if (empty($this->getFixturesToLoad())) {
            return iterator_to_array($this->allFixtures, false);
        }

        $fixtures = [];
        foreach ($this->allFixtures as $fixture) {
            if (!\in_array($fixture::class, $this->getFixturesToLoad(), true)) {
                continue;
            }

            $fixtures[] = $fixture;
        }

        return $fixtures;
    }
}
