<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Profiler\FixtureReference;

use Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference\FixtureReference;
use Neusta\Pimcore\FixtureBundle\Profiler\FixtureReference\FixtureReferenceResolver;
use Neusta\Pimcore\FixtureBundle\Tests\Profiler\FixtureReference\Mock\MockChildFixture;
use Neusta\Pimcore\FixtureBundle\Tests\Profiler\FixtureReference\Mock\MockFixtureDependsOnChild;
use Neusta\Pimcore\FixtureBundle\Tests\Profiler\FixtureReference\Mock\MockFixtureWithoutDependencies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FixtureReferenceResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_does_not_fail_if_it_is_not_provided_any_fixture(): void
    {
        $referenceResolver = new FixtureReferenceResolver();

        self::assertEmpty($referenceResolver->getAllReferences());
        self::assertEmpty($referenceResolver->getRootReferences());
    }

    /**
     * @test
     */
    public function it_works_with_an_ampty_list_of_fixtures(): void
    {
        $referenceResolver = new FixtureReferenceResolver();
        $referenceResolver->setFixtures([]);

        self::assertEmpty($referenceResolver->getAllReferences());
        self::assertEmpty($referenceResolver->getRootReferences());
    }

    /**
     * @test
     */
    public function it_resolves_a_fixture_without_dependencies_as_a_root_fixture(): void
    {
        $mockFixtureWithoutDependencies = new MockFixtureWithoutDependencies();
        $fixtureReference = new FixtureReference($mockFixtureWithoutDependencies);

        $referenceResolver = new FixtureReferenceResolver();
        $referenceResolver->setFixtures([$mockFixtureWithoutDependencies]);

        self::assertEquals([$fixtureReference], $referenceResolver->getAllReferences());
        self::assertEquals([$fixtureReference], $referenceResolver->getRootReferences());
    }

    /**
     * @test
     */
    public function it_resolves_a_fixture_with_dependency_not_as_a_root_fixture(): void
    {
        $mockFixtureDependsOnChild = new MockFixtureDependsOnChild();
        $mockChildFixture = new MockChildFixture();

        $parentFixtureReference = new FixtureReference($mockFixtureDependsOnChild);
        $childFixtureReference = new FixtureReference($mockChildFixture);
        $parentFixtureReference->addDependencyReference($childFixtureReference);
        $childFixtureReference->addDependantReference($parentFixtureReference);

        $referenceResolver = new FixtureReferenceResolver();
        $referenceResolver->setFixtures([$mockFixtureDependsOnChild, $mockChildFixture]);

        self::assertEquals([$parentFixtureReference, $childFixtureReference], $referenceResolver->getAllReferences());
        self::assertEquals([$parentFixtureReference], $referenceResolver->getRootReferences());
    }
}
