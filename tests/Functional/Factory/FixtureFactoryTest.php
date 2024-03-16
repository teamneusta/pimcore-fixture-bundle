<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Functional\Factory;

use Neusta\Pimcore\FixtureBundle\Event\CreateFixture;
use Neusta\Pimcore\FixtureBundle\Event\FixtureWasCreated;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureFactory;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiatorForAll;
use Neusta\Pimcore\FixtureBundle\Fixture;
use Neusta\Pimcore\FixtureBundle\Tests\Fixtures\DependantFixture;
use Neusta\Pimcore\FixtureBundle\Tests\Fixtures\FixtureWithDependency;
use Neusta\Pimcore\FixtureBundle\Tests\Fixtures\SomeFixture;
use Pimcore\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class FixtureFactoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @test
     */
    public function it_creates_a_fixture(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);

        $factory->createFixtures([SomeFixture::class]);

        $fixture = $factory->getFixture(SomeFixture::class);
        self::assertInstanceOf(SomeFixture::class, $fixture);
        self::assertTrue($fixture->created);
    }

    /**
     * @test
     */
    public function it_throws_when_prompted_to_create_a_fixture_that_does_not_implement_the_Fixture_interface(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "stdClass" to implement "Neusta\Pimcore\FixtureBundle\Fixture", but it does not.');

        $factory->createFixtures([\stdClass::class]);
    }

    /**
     * @test
     */
    public function it_creates_depending_fixtures_first(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);

        $factory->createFixtures([FixtureWithDependency::class]);

        $fixtureWithDependency = $factory->getFixture(FixtureWithDependency::class);
        $dependantFixture = $factory->getFixture(DependantFixture::class);
        self::assertInstanceOf(FixtureWithDependency::class, $fixtureWithDependency);
        self::assertInstanceOf(DependantFixture::class, $dependantFixture);
        self::assertGreaterThan($dependantFixture->createdAt, $fixtureWithDependency->createdAt);
    }

    /**
     * @test
     */
    public function it_throws_when_dependency_has_invalid_type(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);
        $fixture = new class() implements Fixture {
            public function create($something): void
            {
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/Parameter "\$something" of .+::create\(\) has an invalid type/');

        $factory->createFixtures([$fixture::class]);
    }

    /**
     * @test
     */
    public function it_throws_when_depending_on_a_non_fixture_object(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);
        $fixture = new class() implements Fixture {
            public function create(\stdClass $noFixture): void
            {
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected "stdClass" to implement "Neusta\Pimcore\FixtureBundle\Fixture", but it does not.');

        $factory->createFixtures([$fixture::class]);
    }

    /**
     * @test
     */
    public function it_creates_a_fixture_only_once(): void
    {
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()]);

        $factory->createFixtures([FixtureWithDependency::class]);

        $dependantFixture = $factory->getFixture(DependantFixture::class);

        $factory->createFixtures([DependantFixture::class]);

        self::assertSame($dependantFixture, $factory->getFixture(DependantFixture::class));
    }

    /**
     * @test
     */
    public function it_dispatches_fixture_creation_events(): void
    {
        $eventDispatcher = new EventDispatcher();
        $factory = new FixtureFactory([new FixtureInstantiatorForAll()], $eventDispatcher);
        $createFixtureEvent = null;
        $fixtureWasCreatedEvent = null;

        $eventDispatcher->addListener(CreateFixture::class, function ($event) use (&$createFixtureEvent) {
            $createFixtureEvent = $event;
        });
        $eventDispatcher->addListener(FixtureWasCreated::class, function ($event) use (&$fixtureWasCreatedEvent) {
            $fixtureWasCreatedEvent = $event;
        });

        $factory->createFixtures([SomeFixture::class]);

        self::assertEquals(new CreateFixture(SomeFixture::class, 0), $createFixtureEvent);
        self::assertEquals(new FixtureWasCreated($factory->getFixture(SomeFixture::class)), $fixtureWasCreatedEvent);
    }
}
