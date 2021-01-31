<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class SaveTest extends ModuleManagerTest
{
    private $method = "save";

    public function testSave () : void
    {
        $filesystem = $this->getMockFilesystem();
        $moduleManager = $this->getMockManager($filesystem, $this->method);

        // If I have a save function
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        // I will need to get my modules directory
        $directory = "directory";
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($directory);
        // And my tracker file name
        $trackerFileName = "tracker";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);
        // And check if that directory exists
        $filesystem->shouldReceive("isDirectory")->withArgs([$directory])->andReturn(false);
        // And create that directory if it doesn't exist
        $filesystem->shouldReceive("makeDirectory")->withArgs([$directory, 0755, true]);

        // Next we get the json options for out tracker content
        $moduleManager->shouldReceive("getJsonOptions")->andReturn([1, 2]);

        // And save the tracker content to the tracker file
        $trackerContent =  ["content"];
        $filesystem->expects("put")->withArgs(["$directory/$trackerFileName", json_encode($trackerContent, 3)]);

        $uut->invoke($moduleManager, $trackerContent);
    }
}
