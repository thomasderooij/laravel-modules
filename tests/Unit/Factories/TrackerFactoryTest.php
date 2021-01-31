<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\Factories;

use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;
use Thomasderooij\LaravelModules\Factories\TrackerFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class TrackerFactoryTest extends Test
{
    public function testCreatingATrackerFile () : void
    {
        $filesystem = \Mockery::mock(Filesystem::class);
        $this->instance('files', $filesystem);

        $moduleManager = \Mockery::mock(ModuleManager::class);
        $this->instance("modules.service.manager", $moduleManager);

        // This function is received from the file factory, which has its own test, so we don't test it again here
        /** @var MockInterface&TrackerFactory $uut */
        $uut = \Mockery::mock(TrackerFactory::class . "[populateFile]", [$filesystem, $moduleManager]);
        $uut->shouldAllowMockingProtectedMethods();
        $this->instance("module.factory.tracker", $uut);

        // I should get the tracker file name
        $trackerFileName = "tracker";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);

        // I should call the populate file function
        $root = "modules_root";
        $trackerStub = $stub = realpath(__DIR__ . '/../../../src/Factories/stubs/tracker.stub');
        $uut->shouldReceive("populateFile")->withArgs([base_path($root), $trackerFileName, $trackerStub]);

        $uut->create($root);
    }
}
