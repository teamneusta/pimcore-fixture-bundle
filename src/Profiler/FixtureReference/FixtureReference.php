<?php

namespace Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

class FixtureReference
{
    /** @var class-string */
    private string $fixtureFqcn;
    /** @var list<FixtureReference> */
    private array $dependencies = [];
    /** @var list<FixtureReference> */
    private array $dependants = [];

    public function __construct(Fixture $fixture)
    {
        $this->fixtureFqcn = get_class($fixture);
    }

    /**
     * @return class-string
     */
    public function getName(): string
    {
        return $this->fixtureFqcn;
    }

    public function addDependencyReference(FixtureReference $dependency): void
    {
        $this->dependencies[] = $dependency;
    }

    public function addDependantReference(FixtureReference $fixtureReference): void
    {
        $this->dependants[] = $fixtureReference;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function hasDependencies(): bool
    {
        return !empty($this->dependencies);
    }

    public function getDependants(): array
    {
        return $this->dependants;
    }

    public function hasDependants(): bool
    {
        return !empty($this->dependants);
    }
}
