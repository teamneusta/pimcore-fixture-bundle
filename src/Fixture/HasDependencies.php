<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

/**
 * The HasDependencies interface needs to be implemented by fixtures which depend on other fixtures.
 *
 * It is inspired by: \Doctrine\Common\DataFixtures\DependentFixtureInterface
 */
interface HasDependencies
{
    /**
     * This method must return a list of fixtures classes on which the implementing class depends on.
     *
     * @return list<class-string<Fixture>>
     */
    public function getDependencies(): array;
}
