<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Sorter;

use Neusta\Pimcore\FixtureBundle\Sorter\CircularFixtureDependencyException;
use Neusta\Pimcore\FixtureBundle\Sorter\FixtureDependencySorter;
use Neusta\Pimcore\FixtureBundle\Sorter\UnresolvedFixtureDependency;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockDependentFixtureWithoutDependencies;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureAlphaDependsOnBeta;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureBetaDependsOnGamma;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureDependsOnItself;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureGammaDependsOnAlpha;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureOneDependsOnTwo;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureTwoDependsOnOne;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureWithoutDependencies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FixtureDependencySorterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sorts_an_empty_list(): void
    {
        $fixtureDependencySorter = new FixtureDependencySorter([]);
        $sorted = $fixtureDependencySorter->sort();

        self::assertEmpty($sorted);
    }

    /**
     * @test
     */
    public function it_sorts_a_list_with_one_entry(): void
    {
        $fixture = new MockFixtureWithoutDependencies();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture]);
        $sorted = $fixtureDependencySorter->sort();

        self::assertCount(1, $sorted);
        self::assertEquals([$fixture], $sorted);
    }

    /**
     * @test
     */
    public function it_sorts_two_fixtures_having_no_dependencies(): void
    {
        $fixture1 = new MockFixtureWithoutDependencies();
        $fixture2 = new MockDependentFixtureWithoutDependencies();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture1, $fixture2]);
        $sorted = $fixtureDependencySorter->sort();

        self::assertCount(2, $sorted);
        self::assertEquals([$fixture1, $fixture2], $sorted);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_one_fixture_depending_on_itself(): void
    {
        $fixture = new MockFixtureDependsOnItself();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture]);

        $this->expectException(CircularFixtureDependencyException::class);
        $this->expectExceptionMessage(
            'CircularFixtureDependency: Circular Reference detected in Fixture "Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureDependsOnItself"'
        );
        $fixtureDependencySorter->sort();
    }

    /**
     * @test
     */
    public function it_throws_exception_for_one_fixture_with_unresolved_dependency(): void
    {
        $fixture = new MockFixtureOneDependsOnTwo();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture]);

        $this->expectException(UnresolvedFixtureDependency::class);
        $this->expectExceptionMessage(
            'UnresolvedFixtureDependency: Fixture "Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureTwoDependsOnOne" not found'
        );
        $fixtureDependencySorter->sort();
    }

    /**
     * @test
     */
    public function it_throws_exception_for_two_fixture_depending_on_each_other(): void
    {
        $fixture1 = new MockFixtureOneDependsOnTwo();
        $fixture2 = new MockFixtureTwoDependsOnOne();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture1, $fixture2]);

        $this->expectException(CircularFixtureDependencyException::class);
        $this->expectExceptionMessage(
            'CircularFixtureDependency: Circular Reference detected in Fixture "Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureOneDependsOnTwo"'
        );
        $fixtureDependencySorter->sort();
    }

    /**
     * @test
     */
    public function it_throws_exception_for_three_fixture_with_circular_dependency(): void
    {
        $fixture1 = new MockFixtureAlphaDependsOnBeta();
        $fixture2 = new MockFixtureBetaDependsOnGamma();
        $fixture3 = new MockFixtureGammaDependsOnAlpha();

        $fixtureDependencySorter = new FixtureDependencySorter([$fixture1, $fixture2, $fixture3]);

        $this->expectException(CircularFixtureDependencyException::class);
        $this->expectExceptionMessage(
            'CircularFixtureDependency: Circular Reference detected in Fixture "Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureAlphaDependsOnBeta"'
        );
        $fixtureDependencySorter->sort();
    }
}
