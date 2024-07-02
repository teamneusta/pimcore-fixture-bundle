<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

/**
 * @internal
 */
final class FixtureReferenceResolver
{
    /** @var array<class-string, FixtureReference> */
    private array $allReferences = [];
    /** @var list<FixtureReference> */
    private array $rootReferences = [];

    /**
     * @param list<Fixture> $loadedFixtures
     */
    public function setFixtures(array $loadedFixtures): void
    {
        $this->initAllReferences($loadedFixtures);
        $this->resolveDependencies($this->createIndexedListOfFixtures($loadedFixtures));
        $this->collectRootReferences();
    }

    /**
     * @param list<Fixture> $loadedFixtures
     */
    private function initAllReferences(array $loadedFixtures): void
    {
        $this->allReferences = [];

        foreach ($loadedFixtures as $fixture) {
            $fixtureReference = new FixtureReference($fixture);
            $this->allReferences[$fixtureReference->getName()] = $fixtureReference;
        }
    }

    /**
     * @param list<Fixture> $loadedFixtures
     *
     * @return array<class-string, Fixture>
     */
    private function createIndexedListOfFixtures(array $loadedFixtures): array
    {
        $indexedList = [];

        foreach ($loadedFixtures as $fixture) {
            $indexedList[$fixture::class] = $fixture;
        }

        return $indexedList;
    }

    /**
     * @param array<class-string, Fixture> $indexedListOfFixtures
     */
    private function resolveDependencies(array $indexedListOfFixtures): void
    {
        foreach ($this->allReferences as $name => $reference) {
            $this->collect($reference, $indexedListOfFixtures[$name]);
        }
    }

    private function collect(FixtureReference $fixtureReference, Fixture $fixture): void
    {
        if (!$fixture instanceof HasDependencies) {
            return;
        }

        foreach ($fixture->getDependencies() as $dependencyFixtureFqcn) {
            $dependencyReference = $this->allReferences[$dependencyFixtureFqcn];
            $fixtureReference->addDependencyReference($dependencyReference);
            $dependencyReference->addDependantReference($fixtureReference);
        }
    }

    private function collectRootReferences(): void
    {
        $this->rootReferences = [];

        foreach ($this->allReferences as $reference) {
            if (!$reference->hasDependants()) {
                $this->rootReferences[] = $reference;
            }
        }
    }

    /**
     * @return list<FixtureReference>
     */
    public function getAllReferences(): array
    {
        return array_values($this->allReferences);
    }

    /**
     * @return list<FixtureReference>
     */
    public function getRootReferences(): array
    {
        return $this->rootReferences;
    }
}
