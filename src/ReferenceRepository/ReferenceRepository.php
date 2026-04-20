<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\ReferenceRepository;

/**
 * Based on \Doctrine\Common\DataFixtures\ReferenceRepository.
 */
interface ReferenceRepository
{
    /**
     * Sets the reference entry identified by $name and referenced to $reference.
     * If $name is already set, it will be overwritten.
     */
    public function setReference(string $name, object $reference): void;

    /**
     * Sets the reference entry identified by $name and referenced to $reference.
     * $name must not be set yet.
     *
     * @throws \BadMethodCallException - if the repository already has a reference by $name
     */
    public function addReference(string $name, object $reference): void;

    /**
     * Loads an object using a stored reference named by $name.
     *
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws \OutOfBoundsException - if the reference does not exist
     */
    public function getReference(string $name, string $class): object;

    /**
     * Check if an object is stored using a reference named by $name.
     *
     * @param class-string $class
     */
    public function hasReference(string $name, string $class): bool;
}
