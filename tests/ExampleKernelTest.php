<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExampleKernelTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function symfony_service_definitions_must_compile(): void
    {
        // when this test passed, it means that the kernel could be loaded and there are no compiling errors in the
        // symfony service definitions.
        $this->expectNotToPerformAssertions();
    }
}
