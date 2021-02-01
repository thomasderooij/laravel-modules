<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Factories\EventServiceProviderFactory;

class EventServiceProviderFactoryTest extends ServiceProviderFactoryTest
{
    public function testGetStub () : void
    {
        $uut = $this->getMethodFromClass("getStub", EventServiceProviderFactory::class);
        $factory = Mockery::mock(EventServiceProviderFactory::class);

        // If I ask for the stub from this package
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/eventServiceProvider.stub");

        // If should be a real file
        /** @var Filesystem $filesystem */
        $filesystem = $this->app->make('files');
        $this->assertTrue($filesystem->isFile($stub));

        // And the stub should be returned by the function
        $this->assertSame($stub, $uut->invoke($factory));
    }

    public function testGetClassName () : void
    {
        $uut = $this->getMethodFromClass("getClassName", EventServiceProviderFactory::class);
        $provider = Mockery::mock(EventServiceProviderFactory::class);

        // I expect to get EventServiceProvider as a classname
        $expected = "EventServiceProvider";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }
}
