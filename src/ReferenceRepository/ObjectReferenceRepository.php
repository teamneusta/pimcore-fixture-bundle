<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\ReferenceRepository;

/**
 * based on \Doctrine\Common\DataFixtures\ReferenceRepository.
 *
 * @template T of object
 */
final class ObjectReferenceRepository
{
    /**
     * @var array<class-string, array<string, T>>
     */
    private array $referencesByClass = [];

    /**
     * @param T $reference
     */
    public function setReference(string $name, object $reference): void
    {
        $this->referencesByClass[$reference::class][$name] = $reference;
    }

    /**
     * @param T $object - managed object
     *
     * @throws \BadMethodCallException - if repository already has a reference by $name
     */
    public function addReference(string $name, object $object): void
    {
        if (isset($this->referencesByClass[$object::class][$name])) {
            throw new \BadMethodCallException(sprintf(
                'Reference to "%s" for class "%s" already exists, use method setReference() in order to override it',
                $name,
                $object::class
            ));
        }

        $this->setReference($name, $object);
    }

    /**
     * Loads an object using stored reference
     * named by $name.
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws \OutOfBoundsException - if repository does not exist
     */
    public function getReference(string $name, string $class): object
    {
        if (!$this->hasReference($name, $class)) {
            throw new \OutOfBoundsException(sprintf('Reference to "%s" for class "%s" does not exist', $name, $class));
        }

        return $this->referencesByClass[$class][$name];
    }

    /**
     * Check if an object is stored using reference
     * named by $name.
     *
     * @param class-string $class
     */
    public function hasReference(string $name, string $class): bool
    {
        return isset($this->referencesByClass[$class][$name]);
    }
}
