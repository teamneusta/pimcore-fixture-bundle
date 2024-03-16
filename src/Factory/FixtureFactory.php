<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory;

use Neusta\Pimcore\FixtureBundle\Event\CreateFixture;
use Neusta\Pimcore\FixtureBundle\Event\FixtureWasCreated;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiator;
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Cache;
use Pimcore\Model\Version;
use Psr\EventDispatcher\EventDispatcherInterface;

class FixtureFactory
{
    /** @var array<class-string<Fixture>, Fixture> */
    private array $instances = [];

    /**
     * @param list<FixtureInstantiator> $instantiators
     */
    public function __construct(
        private array $instantiators,
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {
    }

    /**
     * @param list<class-string<Fixture>> $fixtures
     */
    public function createFixtures(array $fixtures): void
    {
        $versionEnabled = Version::isEnabled();
        $cacheEnabled = Cache::isEnabled();
        Version::disable();
        Cache::disable();

        foreach ($fixtures as $fixtureClass) {
            $this->ensureIsFixture($fixtureClass);
            $this->createFixture($fixtureClass, 0);
        }

        $versionEnabled && Version::enable();
        $cacheEnabled && Cache::enable();
    }

    /**
     * @template T of Fixture
     *
     * @param class-string<T> $fixture
     *
     * @return T
     *
     * @throws \RuntimeException when the fixture hasn't been created
     */
    public function getFixture(string $fixture): Fixture
    {
        if (!isset($this->instances[$fixture])) {
            throw new \RuntimeException(sprintf('Fixture "%s" has not been created.', $fixture));
        }

        $instance = $this->instances[$fixture];
        \assert($instance instanceof $fixture);

        return $instance;
    }

    /**
     * @param class-string<Fixture> $fixtureClass
     */
    private function createFixture(string $fixtureClass, int $level): void
    {
        if (isset($this->instances[$fixtureClass])) {
            return;
        }

        $this->eventDispatcher?->dispatch(new CreateFixture($fixtureClass, $level));

        $this->instances[$fixtureClass] = $this->instantiateFixture($fixtureClass);

        $args = [];
        foreach ($this->getDependencies($fixtureClass, 'create') as $dependentFixtureClass) {
            $this->createFixture($dependentFixtureClass, $level + 1);
            $args[] = $this->instances[$dependentFixtureClass];
        }

        $this->instances[$fixtureClass]->create(...$args);

        $this->eventDispatcher?->dispatch(new FixtureWasCreated($this->instances[$fixtureClass]));
    }

    /**
     * @param class-string<Fixture> $fixtureClass
     */
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
                    // @phpstan-ignore-next-line this is a method parameter, so it always has a class
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
