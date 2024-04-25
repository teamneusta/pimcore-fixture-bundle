<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixtures;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Neusta\Pimcore\FixtureBundle\ReferenceRepository\DataObjectReferenceRepository;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFixture implements ORMFixtureInterface
{
    private DataObjectReferenceRepository $dataObjectReferenceRepository;

    #[Required]
    /**
     * @internal
     */
    public function setDataObjectReferenceRepository(DataObjectReferenceRepository $dataObjectReferenceRepository): void
    {
        $this->dataObjectReferenceRepository = $dataObjectReferenceRepository;
    }

    protected function setReference(string $name, object $reference): void
    {
        $this->dataObjectReferenceRepository->setReference($name, $reference);
    }

    protected function addReference(string $name, object $object): void
    {
        $this->dataObjectReferenceRepository->addReference($name, $object);
    }

    /**
     * @param class-string $class
     */
    protected function getReference(string $name, string $class): object
    {
        return $this->dataObjectReferenceRepository->getReference($name, $class);
    }

    /**
     * @param class-string $class
     */
    protected function hasReference(string $name, string $class): bool
    {
        return $this->dataObjectReferenceRepository->hasReference($name, $class);
    }
}
