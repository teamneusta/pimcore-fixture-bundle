<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Factory\FixtureInstantiator;

use NspPimcore\FixtureBase\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FixtureInstantiatorForParametrizedConstructors implements FixtureInstantiator
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function supports(string $fixtureClass): bool
    {
        if (!$constructor = (new \ReflectionClass($fixtureClass))->getConstructor()) {
            return false;
        }

        return $constructor->isPublic() && $constructor->getNumberOfRequiredParameters() > 0;
    }

    public function instantiate(string $fixtureClass): Fixture
    {
        $reflectionClass = new \ReflectionClass($fixtureClass);
        $constructor = $reflectionClass->getConstructor();

        $constructorServices = [];
        foreach ($constructor->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();

            if (!$reflectionType) {
                $this->throwLogicException(
                    'Parameter "$%s" of %s::%s() has no type, while it should be an implementation of "%s".',
                    $parameter,
                );
            }

            $reflectionTypeName = $reflectionType->getName();
            if (ContainerInterface::class === $reflectionTypeName) {
                $constructorServices[] = $this->container;
                continue;
            }
            if ($this->container->has($reflectionTypeName)) {
                $constructorServices[] = $this->container->get($reflectionTypeName);
                continue;
            }

            $this->throwLogicException(
                'Parameter "$%s" of %s::%s() is not known to container. Check if it exists and is public. "%s"',
                $parameter,
            );
        }

        return new $fixtureClass(...$constructorServices);
    }

    private function throwLogicException(string $message, \ReflectionParameter $parameter): void
    {
        throw new \LogicException(
            sprintf(
                $message,
                $parameter->getName(),
                $parameter->getDeclaringClass()->getName(),
                $parameter->getDeclaringFunction()->getName(),
                Fixture::class,
            ),
        );
    }
}
