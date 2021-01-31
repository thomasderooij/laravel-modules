<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class HasTrackerFileTest extends ModuleManagerTest
{
    private $method = "hasTrackerFile";

    public function testHasTrackerFile () : void
    {
        $filesystem = $this->getMockFilesystem();
        // If want to know if there is a tracker file
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);
        $moduleManager = $this->getMockManager($filesystem, $this->method);

        // I first fetch the modules directory
        $root = base_path("root_dir");
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($root);

        // And I fetch the tracker file name
        $trackerFileName = "trackerFile";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);

        // And I should see if the tracker file exists
        $filesystem->shouldReceive("isFile")->withArgs(["$root/$trackerFileName"])->andReturn(true);

        // Then I should get a confirmation
        $this->assertTrue($uut->invoke($moduleManager));
    }

    public function testDoesNotHaveTrackerFile () : void
    {
        $filesystem = $this->getMockFilesystem();
        // If want to know if there is a tracker file
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);
        $moduleManager = $this->getMockManager($filesystem, $this->method);

        // I first fetch the modules directory
        $root = base_path("root_dir");
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($root);

        // And I fetch the tracker file name
        $trackerFileName = "trackerFile";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);

        // And I should see if the tracker file exists
        $filesystem->shouldReceive("isFile")->withArgs(["$root/$trackerFileName"])->andReturn(false);

        // Then I should get a false
        $this->assertFalse($uut->invoke($moduleManager));
    }
}
