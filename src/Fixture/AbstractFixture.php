<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

use Neusta\Pimcore\FixtureBundle\ReferenceRepository\ObjectReferenceRepository;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFixture implements FixtureInterface
{
    private ObjectReferenceRepository $objectReferenceRepository;

    #[Required]
    /**
     * @internal
     */
    public function setObjectReferenceRepository(ObjectReferenceRepository $objectReferenceRepository): void
    {
        $this->objectReferenceRepository = $objectReferenceRepository;
    }

    protected function setReference(string $name, object $reference): void
    {
        $this->objectReferenceRepository->setReference($name, $reference);
    }

    protected function addReference(string $name, object $object): void
    {
        $this->objectReferenceRepository->addReference($name, $object);
    }

    /**
     * @param class-string $class
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
