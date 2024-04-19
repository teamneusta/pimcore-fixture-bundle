<?php

namespace Neusta\Pimcore\FixtureBundle\Fixtures;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Neusta\Pimcore\FixtureBundle\ReferenceRepository\DataObjectReferenceRepository;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFixture implements FixtureInterface, ORMFixtureInterface
{
    private DataObjectReferenceRepository $dataObjectReferenceRepository;

    #[Required]
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

    protected function getReference(string $name, string $class)
    {
        return $this->dataObjectReferenceRepository->getReference($name, $class);
    }

    protected function hasReference(string $name, string $class): bool
    {
        return $this->dataObjectReferenceRepository->hasReference($name, $class);
    }
}
