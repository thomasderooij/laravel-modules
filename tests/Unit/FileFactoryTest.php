<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Factories\FileFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class FileFactoryTest extends Test
{
    /**
     * Here we test populating a file in an exiting directory
     */
    public function testPopulateFileInExistingDir () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        // If I have a file factory
        $mockFactory = $this->getMockFactory($mockFilesystem);

        // I expect the filesystem to fetch a stub file
        $mockFilesystem
            ->expects('get')
            ->withArgs([base_path("test_file.php")])
            ->andReturn("I'm a text with {replaceable} elements")
            ->once()
        ;

        // And I expect the filesystem to write a new file, based on the stub
        $mockFilesystem
            ->expects('put')
            ->withArgs([base_path("new_file.php"), "I'm a text with cool elements"])
            ->once()
        ;

        // And the ensureSlash function should be called
        $mockFactory->expects("ensureSlash")->withArgs([base_path()])->andReturn(base_path() . "/")->once();

        $reflection = new \ReflectionClass(FileFactory::class);
        $uut = $reflection->getMethod("populateFile"); // The unit under test is this specific method
        $uut->setAccessible(true);

        // When I call the populateFile function
        $uut->invoke($mockFactory, base_path(), "new_file.php", base_path("test_file.php"), ["{replaceable}" => "cool"]);
    }

    /**
     * Here we test creating populating a file in a directory that doesn't exit yet
     */
    public function testPopulateFileInNonExistingDir () : void
    {
        $mockFilesystem = Mockery::mock(Filesystem::class);
        // If I have a file factory
        $mockFactory = $this->getMockFactory($mockFilesystem);

        // I expect the filesystem to fetch a stub file
        $mockFilesystem
            ->expects('get')
            ->withArgs([base_path("test_file.php")])
            ->andReturn("I'm a text with {replaceable} elements")
            ->once()
        ;

        $dir = "new_dir";
        $mockFilesystem
            ->expects("makeDirectory")
            ->withArgs([base_path($dir), 0755, true])
            ->once()
        ;

        // And I expect the filesystem to write a new file, based on the stub
        $mockFilesystem
            ->expects('put')
            ->withArgs([base_path("$dir/new_file.php"), "I'm a text with cool elements"])
            ->once()
        ;

        // And the ensureSlash function should be called
        $mockFactory->expects("ensureSlash")->withArgs([base_path($dir)])->andReturn(base_path($dir) . "/")->once();

        $reflection = new \ReflectionClass(FileFactory::class);
        $uut = $reflection->getMethod("populateFile"); // The unit under test is this specific method
        $uut->setAccessible(true);

        // When I call the populateFile function
        $uut->invoke($mockFactory, base_path($dir), "new_file.php", base_path("test_file.php"), ["{replaceable}" => "cool"]);
    }

    private function getMockFactory (Filesystem $filesystem) : Mockery\MockInterface
    {
        $mockFactory = Mockery::mock(FileFactory::class."[ensureSlash]", [
            $filesystem,
            $this->app->make(ModuleManager::class)
        ]);
        $mockFactory->shouldAllowMockingProtectedMethods();

        return $mockFactory;
    }
}
