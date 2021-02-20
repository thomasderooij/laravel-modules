<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class SaveTest extends ModuleStateRepositoryTest
{
    private $method = "save";

    public function testSave () : void
    {
        $moduleManager = $this->getMockRepository($this->method);

        // If I have a save function
        $uut = $this->getMethod($this->method);

        // I will need to get my modules directory
        $directory = "directory";
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($directory);
        // And my tracker file name
        $trackerFileName = "tracker";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);
        // And check if that directory exists
        $this->filesystem->shouldReceive("isDirectory")->withArgs([$directory])->andReturn(false);
        // And create that directory if it doesn't exist
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([$directory, 0755, true]);

        // Next we get the json options for out tracker content
        $moduleManager->shouldReceive("getJsonOptions")->andReturn([1, 2]);

        // And save the tracker content to the tracker file
        $trackerContent =  ["content"];
        $this->filesystem->expects("put")->withArgs(["$directory/$trackerFileName", json_encode($trackerContent, 3)]);

        $uut->invoke($moduleManager, $trackerContent);
    }
}
