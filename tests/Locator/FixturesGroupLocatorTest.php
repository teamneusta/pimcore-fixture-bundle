<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Locator;

use Neusta\Pimcore\FixtureBundle\Locator\FixturesGroupLocator;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureOfGroupRed;
use Neusta\Pimcore\FixtureBundle\Tests\Mock\MockFixtureWithoutDependencies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FixturesGroupLocatorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_works_with_empty_lists(): void
    {
        $namedFixtureLocator = new FixturesGroupLocator(new \ArrayObject());
        $namedFixtureLocator->setGroupsToLoad([]);

        $locatedFixtures = $namedFixtureLocator->getFixtures();
        self::assertSame([], $locatedFixtures);
    }

    /**
     * @test
     */
    public function it_filters_for_wanted_fixture(): void
    {
        $fixture = new MockFixtureOfGroupRed();

        $namedFixtureLocator = new FixturesGroupLocator(new \ArrayObject([$fixture]));
        $namedFixtureLocator->setGroupsToLoad(['red']);

        $locatedFixtures = $namedFixtureLocator->getFixtures();
        self::assertSame([$fixture], $locatedFixtures);
    }

    /**
     * @test
     */
    public function it_skips_unwanted_fixture(): void
    {
        $fixture = new MockFixtureOfGroupRed();

        $namedFixtureLocator = new FixturesGroupLocator(new \ArrayObject([$fixture]));
        $namedFixtureLocator->setGroupsToLoad(['blue']);

        $locatedFixtures = $namedFixtureLocator->getFixtures();
        self::assertSame([], $locatedFixtures);
    }

    /**
     * @test
     */
    public function it_provides_all_fixtures_if_no_filter_is_given(): void
    {
        $fixture = new MockFixtureWithoutDependencies();

        $namedFixtureLocator = new FixturesGroupLocator(new \ArrayObject([$fixture]));

        $locatedFixtures = $namedFixtureLocator->getFixtures();
        self::assertSame([$fixture], $locatedFixtures);
    }
}
