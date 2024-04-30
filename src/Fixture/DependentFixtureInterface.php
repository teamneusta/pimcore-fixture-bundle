<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

/**
 * DependentFixtureInterface needs to be implemented by fixtures which depend on other fixtures
 * inspired by \Doctrine\Common\DataFixtures\DependentFixtureInterface.
 */
interface DependentFixtureInterface extends FixtureInterface
{
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array<class-string<FixtureInterface>>
     */
    public function getDependencies(): array;
}
