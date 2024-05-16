<?php

namespace Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;

class FixtureReferenceResolver
{
    /** @var array<class-string, FixtureReference> */
    private array $allReferences;
    /** @var list<FixtureReference> */
    private array $rootReferences;

    /**
     * @param array<Fixture> $loadedFixtures
     * @return void
     */
    public function setFixtures(array $loadedFixtures): void
    {
        $this->initAllReferences($loadedFixtures);
        $this->resolveDependencies($this->createIndexedListOfFixtures($loadedFixtures));
        $this->collectRootReferences();
    }

    private function initAllReferences(array $loadedFixtures): void
    {
        $this->allReferences = [];

        foreach ($loadedFixtures as $fixture) {
            $fixtureReference = new FixtureReference($fixture);
            $this->allReferences[$fixtureReference->getName()] = $fixtureReference;
        }
    }

    /**
     * @param array<Fixture> $loadedFixtures
     * @return array<class-string, Fixture>
     */
    private function createIndexedListOfFixtures(array $loadedFixtures): array
    {
        $indexedList = [];

        foreach ($loadedFixtures as $fixture) {
            $indexedList[get_class($fixture)] = $fixture;
        }

        return $indexedList;
    }

    /**
     * @param array<class-string, Fixture> $indexedListOfFixtures
     * @return void
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
     * @return array<class-string, FixtureReference>
     */
    public function getAllReferences(): array
    {
        return $this->allReferences;
    }

    /**
     * @return list<FixtureReference>
     */
    public function getRootReferences(): array
    {
        return $this->rootReferences;
    }
}
