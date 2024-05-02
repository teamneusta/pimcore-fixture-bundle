<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

final class FixtureDependencySorter
{
    /** @var array<class-string<Fixture>> */
    private array $checking = [];

    /**
     * @param list<Fixture> $allFixtures
     */
    public function __construct(
        private readonly array $allFixtures,
    ) {
    }

    /**
     * @param list<Fixture> $fixtures
     *
     * @return list<Fixture>
     */
    public function sort(array $fixtures = []): array
    {
        $fixtures = empty($fixtures) ? $this->allFixtures : $fixtures;

        $sorted = [];
        foreach ($fixtures as $fixture) {
            $this->add($fixture, $sorted);
        }

        return $sorted;
    }

    /**
     * @param list<Fixture> $sorted
     */
    private function add(Fixture $fixture, array &$sorted): void
    {
        if (\in_array($fixture, $sorted, true)) {
            return;
        }

        if (\in_array($fixture::class, $this->checking, true)) {
            throw new CircularFixtureDependency($fixture::class);
        }
        $this->checking[] = $fixture::class;

        if (!$fixture instanceof HasDependencies || [] === $fixture->getDependencies()) {
            $sorted[] = $fixture;

            return;
        }

        foreach ($fixture->getDependencies() as $dependency) {
            $this->add($this->getFixture($dependency), $sorted);
        }
        $sorted[] = $fixture;

        $this->checking = array_filter($this->checking, fn ($v) => $v !== $fixture::class);
    }

    private function getFixture(string $name): Fixture
    {
        foreach ($this->allFixtures as $fixture) {
            if ($fixture::class === $name) {
                return $fixture;
            }
        }

        throw new UnresolvedFixtureDependency($name);
    }
}
