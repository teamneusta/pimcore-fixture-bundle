<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class NamedFixtureLocator implements FixtureLocatorInterface
{
    /** @var list<class-string<FixtureInterface>> */
    private array $fixturesToLoad = [];

    /**
     * @param \Traversable<FixtureInterface> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
    ) {
    }

    /**
     * @return list<class-string<FixtureInterface>>
     */
    public function getFixturesToLoad(): array
    {
        return $this->fixturesToLoad;
    }

    /**
     * @param list<class-string<FixtureInterface>> $fixtureNames
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
