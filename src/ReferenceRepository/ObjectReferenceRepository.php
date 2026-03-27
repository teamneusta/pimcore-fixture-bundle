<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\ReferenceRepository;

final class ObjectReferenceRepository implements ReferenceRepository
{
    /**
     * @var array<class-string, array<string, object>>
     */
    private array $referencesByClass = [];

    public function setReference(string $name, object $reference): void
    {
        $this->referencesByClass[$reference::class][$name] = $reference;
    }

    public function addReference(string $name, object $reference): void
    {
        if (isset($this->referencesByClass[$reference::class][$name])) {
            throw new \BadMethodCallException(\sprintf(
                'Reference to "%s" for class "%s" already exists, use method setReference() in order to override it',
                $name,
                $reference::class,
            ));
        }

        $this->setReference($name, $reference);
    }

    public function getReference(string $name, string $class): object
    {
        if (!$this->hasReference($name, $class)) {
            throw new \OutOfBoundsException(\sprintf('Reference to "%s" for class "%s" does not exist', $name, $class));
        }

        $reference = $this->referencesByClass[$class][$name];
        \assert($reference instanceof $class);

        return $reference;
    }

    public function hasReference(string $name, string $class): bool
    {
        return isset($this->referencesByClass[$class][$name]);
    }
}
