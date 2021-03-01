<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class HasTrackerFileTest extends ModuleStateRepositoryTest
{
    private $method = "hasTrackerFile";

    public function testHasTrackerFile () : void
    {
        // If want to know if there is a tracker file
        $uut = $this->getMethod($this->method);
        $moduleManager = $this->getMockRepository($this->method);

        // I first fetch the modules directory
        $root = base_path("root_dir");
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($root);

        // And I fetch the tracker file name
        $trackerFileName = "trackerFile";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);

        // And I should see if the tracker file exists
        $this->filesystem->shouldReceive("isFile")->withArgs(["$root/$trackerFileName"])->andReturn(true);

        // Then I should get a confirmation
        $this->assertTrue($uut->invoke($moduleManager));
    }

    public function testDoesNotHaveTrackerFile () : void
    {
        // If want to know if there is a tracker file
        $uut = $this->getMethod($this->method);
        $moduleManager = $this->getMockRepository($this->method);

        // I first fetch the modules directory
        $root = base_path("root_dir");
        $moduleManager->shouldReceive("getModulesDirectory")->andReturn($root);

        // And I fetch the tracker file name
        $trackerFileName = "trackerFile";
        $moduleManager->shouldReceive("getTrackerFileName")->andReturn($trackerFileName);

        // And I should see if the tracker file exists
        $this->filesystem->shouldReceive("isFile")->withArgs(["$root/$trackerFileName"])->andReturn(false);

        // Then I should get a false
        $this->assertFalse($uut->invoke($moduleManager));
    }
}
