<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class IsInitialisedTest extends ModuleManagerTest
{
    private $method = "isInitialised";

    public function testIsInitialised () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have a config file
        $uut->shouldReceive("hasConfig")->andReturn(true);
        // And I have a tracker file
        $uut->shouldReceive("hasTrackerFile")->andReturn(true);

        // The the modules should be considered to be initialised
        $this->assertTrue($uut->isInitialised());
    }

    public function testCheckingInitialisationIfThereIsNoConfig () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I don't  have a config file
        $uut->shouldReceive("hasConfig")->andReturn(false);
        // But I do have a tracker file
        $uut->shouldReceive("hasTrackerFile")->andReturn(true);

        // The the modules should be considered to not be initialised
        $this->assertFalse($uut->isInitialised());
    }

    public function testCheckingInitialisationIfThereIsNoTrackerFile () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have a config file
        $uut->shouldReceive("hasConfig")->andReturn(true);
        // But I don't have a tracker file
        $uut->shouldReceive("hasTrackerFile")->andReturn(false);

        // The the modules should be considered to not be initialised
        $this->assertFalse($uut->isInitialised());
    }

    public function testCheckingInitialisationWhenNeitherFileIsPresent () : void
    {
        $uut = $this->getMockManager(null, $this->method);

        // If I have a config file
        $uut->shouldReceive("hasConfig")->andReturn(false);
        // And I don't have a tracker file
        $uut->shouldReceive("hasTrackerFile")->andReturn(false);

        // The the modules should be considered to not be initialised
        $this->assertFalse($uut->isInitialised());
    }
}
