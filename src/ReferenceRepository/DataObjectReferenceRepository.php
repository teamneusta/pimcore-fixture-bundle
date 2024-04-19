<?php

namespace Neusta\Pimcore\FixtureBundle\ReferenceRepository;

/**
 * based on \Doctrine\Common\DataFixtures\ReferenceRepository
 */
class DataObjectReferenceRepository
{
    /**
     * @psalm-var array<class-string, array<string, object>>
     */
    private array $referencesByClass = [];

    public function setReference(string $name, object $reference): void
    {
        $class = get_class($reference);

        $this->referencesByClass[$class][$name] = $reference;
    }

    /**
     * @param string $name
     * @param object $object - managed object
     *
     * @return void
     *
     * @throws \BadMethodCallException - if repository already has a reference by $name.
     */
    public function addReference(string $name, object $object): void
    {
        $class = get_class($object);
        if (isset($this->referencesByClass[$class][$name])) {
            throw new \BadMethodCallException(sprintf(
                'Reference to "%s" for class "%s" already exists, use method setReference() in order to override it',
                $name,
                $class
            ));
        }

        $this->setReference($name, $object);
    }

    /**
     * Loads an object using stored reference
     * named by $name
     *
     * @param string $name
     * @param string $class
     * @psalm-param class-string<T> $class
     *
     * @return object
     * @psalm-return T
     *
     * @throws \OutOfBoundsException - if repository does not exist.
     *
     * @template T of object
     */
    public function getReference(string $name, string $class)
    {
        if (! $this->hasReference($name, $class)) {
            throw new \OutOfBoundsException(sprintf('Reference to "%s" for class "%s" does not exist', $name, $class));
        }

        return $this->referencesByClass[$class][$name];
    }

    /**
     * Check if an object is stored using reference
     * named by $name
     *
     * @param string $name
     * @psalm-param class-string $class
     *
     * @return bool
     */
    public function hasReference(string $name, string $class): bool
    {
        return isset($this->referencesByClass[$class][$name]);
    }
}
