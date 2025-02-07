<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

use Neusta\Pimcore\FixtureBundle\ReferenceRepository\ObjectReferenceRepository;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFixture implements Fixture
{
    private ObjectReferenceRepository $objectReferenceRepository;

    /** @internal */
    #[Required]
    public function setObjectReferenceRepository(ObjectReferenceRepository $objectReferenceRepository): void
    {
        $this->objectReferenceRepository = $objectReferenceRepository;
    }

    protected function setReference(string $name, object $reference): void
    {
        $this->objectReferenceRepository->setReference($name, $reference);
    }

    /**
     * @throws \BadMethodCallException - if repository already has a reference by $name
     */
    protected function addReference(string $name, object $reference): void
    {
        $this->objectReferenceRepository->addReference($name, $reference);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws \OutOfBoundsException - if reference does not exist in repository
     */
    protected function getReference(string $name, string $class): object
    {
        return $this->objectReferenceRepository->getReference($name, $class);
    }

    /**
     * @param class-string $class
     */
    protected function hasReference(string $name, string $class): bool
    {
        return $this->objectReferenceRepository->hasReference($name, $class);
    }
}
