<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Factories\AuthServiceProviderFactory;
use Thomasderooij\LaravelModules\Tests\Test;

class AuthServiceProviderFactoryTest extends Test
{
    public function testGetStub () : void
    {
        $uut = $this->getMethodFromClass("getStub", AuthServiceProviderFactory::class);
        $provider = Mockery::mock(AuthServiceProviderFactory::class);

        // If I ask for the sub from this package
        $stub = realpath(__DIR__ . "/../../src/Factories/stubs/authServiceProvider.stub");

        // If should be a real file
        /** @var Filesystem $filesystem */
        $filesystem = $this->app->make('files');
        $this->assertTrue($filesystem->isFile($stub));

        // And the stub should be returned by the function
        $this->assertSame($stub, $uut->invoke($provider));
    }

    public function testGetClassName () : void
    {
        $uut = $this->getMethodFromClass("getClassName", AuthServiceProviderFactory::class);
        $provider = Mockery::mock(AuthServiceProviderFactory::class);

        // I expect to get AuthServiceProvider as a classname
        $expected = "AuthServiceProvider";

        // When I ask for the classname
        $this->assertSame($expected, $uut->invoke($provider));
    }
}
