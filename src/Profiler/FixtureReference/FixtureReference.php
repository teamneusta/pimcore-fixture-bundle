<?php declare(strict_types=1);

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
        $this->fixtureFqcn = $fixture::class;
    }

    /**
     * @return class-string
     */
    public function getName(): string
    {
        return $this->fixtureFqcn;
    }

    public function addDependencyReference(self $dependency): void
    {
        $this->dependencies[] = $dependency;
    }

    public function addDependantReference(self $fixtureReference): void
    {
        $this->dependants[] = $fixtureReference;
    }

    /**
     * @return list<FixtureReference>
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function hasDependencies(): bool
    {
        return !empty($this->dependencies);
    }

    /**
     * @return list<FixtureReference>
     */
    public function getDependants(): array
    {
        return $this->dependants;
    }

    public function hasDependants(): bool
    {
        return !empty($this->dependants);
    }
}
