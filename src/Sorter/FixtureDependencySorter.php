<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class FixtureDependencySorter
{
    /** @var array<class-string<FixtureInterface>> */
    private array $checking = [];

    /**
     * @param list<FixtureInterface> $allFixtures
     */
    public function __construct(
        private readonly array $allFixtures,
    ) {
    }

    /**
     * @param list<FixtureInterface> $fixtures
     *
     * @return list<FixtureInterface>
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
     * @param list<FixtureInterface> $sorted
     */
    private function add(FixtureInterface $fixture, array &$sorted): void
    {
        if (\in_array($fixture, $sorted, true)) {
            return;
        }

        $fixtureName = $fixture::class;

        if (\in_array($fixtureName, $this->checking, true)) {
            throw new CircularFixtureDependencyException($fixtureName);
        }
        $this->checking[] = $fixtureName;

        if (!$fixture instanceof DependentFixtureInterface || [] === $fixture->getDependencies()) {
            $sorted[] = $fixture;

            return;
        }

        foreach ($fixture->getDependencies() as $dependency) {
            $this->add($this->getFixture($dependency), $sorted);
        }
        $sorted[] = $fixture;

        $this->checking = array_filter($this->checking, fn ($v) => $v !== $fixtureName);
    }

    private function getFixture(string $name): FixtureInterface
    {
        foreach ($this->allFixtures as $fixture) {
            if ($fixture::class === $name) {
                return $fixture;
            }
        }

        throw new UnresolvedFixtureDependency($name);
    }
}
