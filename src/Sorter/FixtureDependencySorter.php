<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;
use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class FixtureDependencySorter
{
    /** @var array<class-string<FixtureInterface>> */
    private array $checking = [];

    /**
     * @param array<FixtureInterface> $allFixtures
     */
    public function __construct(
        private readonly array $allFixtures,
    ) {
    }

    /**
     * @param array<FixtureInterface> $fixtures
     *
     * @return array<FixtureInterface>
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
     * @param array<FixtureInterface> $sorted
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

        if (!$fixture instanceof DependentFixtureInterface || empty($fixture->getDependencies())) {
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
