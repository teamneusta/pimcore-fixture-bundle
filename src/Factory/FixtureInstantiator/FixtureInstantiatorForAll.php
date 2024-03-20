<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator;

use Neusta\Pimcore\FixtureBundle\Fixture;

final class FixtureInstantiatorForAll implements FixtureInstantiator
{
    public function supports(string $fixtureClass): bool
    {
        if (!class_exists($fixtureClass)) {
            return false;
        }

        $class = new \ReflectionClass($fixtureClass);

        if ($class->isAbstract()) {
            return false;
        }

        $constructor = $class->getConstructor();

        return !$constructor || ($constructor->isPublic() && 0 === $constructor->getNumberOfRequiredParameters());
    }

    public function instantiate(string $fixtureClass): Fixture
    {
        $fixture = new $fixtureClass();
        \assert($fixture instanceof Fixture);

        return $fixture;
    }
}
