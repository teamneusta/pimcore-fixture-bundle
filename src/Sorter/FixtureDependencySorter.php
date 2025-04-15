<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

final class FixtureDependencySorter
{
    /** @var array<class-string<Fixture>, Fixture> */
    private readonly array $allFixtures;

    /** @var array<class-string<Fixture>> */
    private array $checking = [];

    /**
     * @param iterable<Fixture> $allFixtures
     */
    public function __construct(iterable $allFixtures)
    {
        $indexed = [];
        foreach ($allFixtures as $fixture) {
            $indexed[$fixture::class] = $fixture;
        }

        $this->allFixtures = $indexed;
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

    /**
     * @param class-string<Fixture> $name
     */
    private function getFixture(string $name): Fixture
    {
        return $this->allFixtures[$name] ?? throw new UnresolvedFixtureDependency($name);
    }
}
