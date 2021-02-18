<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetTrackerContentTest extends ModuleStateRepositoryTest
{
    private $method = "getTrackerContent";

    public function testGetTrackerContent () : void
    {
        // If I have a method to ask for the modules tracker key
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockRepository($this->method);
        // We have to check if there is a tracker file
        $moduleManager->shouldReceive("hasTrackerFile")->andReturn(true);
        // We get the modules directory
        $directory = "directory";
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn("$directory");
        // And we get the tracker file name
        $tracker = "tracker";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($tracker);

        // And then we fetch its content
        $content = ["modules" => ["module_1",  "other_module"]];
        $this->filesystem->shouldReceive("get")->withArgs(["$directory/$tracker"])->andReturn(json_encode($content));

        // And we should receive an array
        $this->assertSame($content, $uut->invoke($moduleManager));
    }

    public function testGetTrackerContentWithoutTracker () : void
    {
        // If I have a method to ask for the modules tracker key
        $uut = $this->getMethod($this->method);

        $moduleManager = $this->getMockRepository($this->method);
        // But I don't have a tracker file
        $moduleManager->shouldReceive("hasTrackerFile")->andReturn(false);

        // I expect an exception
        $this->expectException(TrackerFileNotFoundException::class);
        // With a message
        $this->expectExceptionMessage("No tracker file has been located.");

        // When I ask for the tracker content
        $uut->invoke($moduleManager);
    }
}
