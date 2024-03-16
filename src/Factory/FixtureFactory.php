<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory;

use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiator;
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Model\Version;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class FixtureFactory
{
    /** @var array<class-string<Fixture>, Fixture> */
    private array $instances = [];
    /** @var array<class-string, float> */
    private array $executionTimes = [];
    /** @var array<array<class-string, int>> */
    private array $executionTree = [];

    /**
     * @param array<string, class-string<Fixture>> $fixtureMapping
     * @param list<FixtureInstantiator>            $instantiators
     */
    public function __construct(
        private array $fixtureMapping,
        private array $instantiators,
        private ?Profiler $profiler = null,
        private bool $trackExecutionTime = false,
    ) {
    }

    /**
     * @return array<class-string, float>
     */
    public function getExecutionTimes(): array
    {
        return $this->executionTimes;
    }

    /**
     * @return array<array<class-string, int>>
     */
    public function getExecutionTree(): array
    {
        return $this->executionTree;
    }

    /**
     * @param list<string|class-string<Fixture>> $fixtures
     */
    public function createFixtures(array $fixtures): void
    {
        Version::disable();
        Cache::disable();
        $originalSqlLogger = Db::getConnection()->getConfiguration()->getSQLLogger();
        Db::getConnection()->getConfiguration()->setSQLLogger(null);
        $this->profiler?->disable();

        foreach ($fixtures as $fixtureNameOrClass) {
            if ($this->trackExecutionTime) {
                $start = microtime(true);
            }

            $this->ensureIsFixture($fixtureClass = $this->fixtureMapping[$fixtureNameOrClass] ?? $fixtureNameOrClass);
            $this->createFixture($fixtureClass, 0);

            if ($this->trackExecutionTime) {
                $this->executionTimes[$fixtureNameOrClass] = microtime(true) - $start;
            }
        }

        if ($this->trackExecutionTime) {
            $this->executionTimes['All together'] = array_sum($this->executionTimes);
        }

        Db::getConnection()->getConfiguration()->setSQLLogger($originalSqlLogger);
        Version::enable();
        Cache::enable();
    }

    /**
     * @param class-string<Fixture> $fixtureClass
     */
    private function createFixture(string $fixtureClass, int $level): void
    {
        if ($this->trackExecutionTime) {
            $this->executionTree[] = ['fixtureClass' => $fixtureClass, 'level' => $level];
        }

        if (isset($this->instances[$fixtureClass])) {
            return;
        }

        $this->instances[$fixtureClass] = $this->instantiateFixture($fixtureClass);

        $args = [];
        foreach ($this->getDependencies($fixtureClass, 'create') as $dependentFixtureClass) {
            $this->createFixture($dependentFixtureClass, $level + 1);
            $args[] = $this->instances[$dependentFixtureClass];
        }

        $this->instances[$fixtureClass]->create(...$args);
    }

    private function instantiateFixture(string $fixtureClass): Fixture
    {
        foreach ($this->instantiators as $instantiator) {
            if ($instantiator->supports($fixtureClass)) {
                return $instantiator->instantiate($fixtureClass);
            }
        }

        throw new \RuntimeException(sprintf(
            'No instantiator found that is able to instantiate fixtures of type "%s".',
            $fixtureClass,
        ));
    }

    /**
     * @return list<class-string<Fixture>>
     */
    private function getDependencies(string $fixtureClass, string $methodName): iterable
    {
        foreach ((new \ReflectionMethod($fixtureClass, $methodName))->getParameters() as $parameter) {
            $type = $parameter->getType();

            if (!$type instanceof \ReflectionNamedType) {
                throw new \LogicException(sprintf(
                    'Parameter "$%s" of %s::%s() has an invalid type.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                    $parameter->getDeclaringFunction()->getName(),
                ));
            }

            $this->ensureIsFixture($type->getName());

            yield $type->getName();
        }
    }

    /** @phpstan-assert class-string<Fixture> $fixtureClass */
    private function ensureIsFixture(string $fixtureClass): void
    {
        if (!is_a($fixtureClass, Fixture::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" to implement "%s", but it does not.',
                $fixtureClass,
                Fixture::class,
            ));
        }
    }
}
