<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator;

use Neusta\Pimcore\FixtureBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FixtureInstantiatorForParametrizedConstructors implements FixtureInstantiator
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function supports(string $fixtureClass): bool
    {
        if (!$constructor = $this->getConstructor($fixtureClass)) {
            return false;
        }

        return $constructor->isPublic() && $constructor->getNumberOfRequiredParameters() > 0;
    }

    public function instantiate(string $fixtureClass): Fixture
    {
        $constructor = $this->getConstructor($fixtureClass);
        \assert($constructor instanceof \ReflectionMethod);

        $constructorServices = [];
        foreach ($constructor->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();

            if (!$reflectionType instanceof \ReflectionNamedType) {
                $this->throwLogicException(
                    'Parameter "$%s" of %s::%s() has no type, while it should be an implementation of "%s".',
                    $parameter,
                );
            }

            $type = $reflectionType->getName();

            if (ContainerInterface::class === $type) {
                $constructorServices[] = $this->container;
                continue;
            }

            if ($this->container->has($type)) {
                $constructorServices[] = $this->container->get($type);
                continue;
            }

            $this->throwLogicException(
                'Parameter "$%s" of %s::%s() is not known to container. Check if it exists and is public. "%s"',
                $parameter,
            );
        }

        $fixture = new $fixtureClass(...$constructorServices);
        \assert($fixture instanceof Fixture);

        return $fixture;
    }

    /**
     * @param class-string<Fixture> $fixtureClass
     */
    private function getConstructor(string $fixtureClass): ?\ReflectionMethod
    {
        return (new \ReflectionClass($fixtureClass))->getConstructor();
    }

    private function throwLogicException(string $message, \ReflectionParameter $parameter): never
    {
        throw new \LogicException(sprintf(
            $message,
            $parameter->getName(),
            // @phpstan-ignore-next-line this is a constructor parameter, so it always has a class
            $parameter->getDeclaringClass()->getName(),
            $parameter->getDeclaringFunction()->getName(),
            Fixture::class,
        ));
    }
}
