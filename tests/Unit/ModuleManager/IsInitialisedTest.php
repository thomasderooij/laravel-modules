<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class IsInitialisedTest extends ModuleManagerTest
{
    public function testHasConfigAndTrackerFile () : void
    {
        $filesystem = $this->getMockFilesystem();

        // I the config specifies a module root dir
        $rootDir = "root_dir";
        Config::shouldReceive('get')->withArgs(['modules.root', null])->andReturn($rootDir);

        // And there is a modules tracker file
        $filesystem
            ->shouldReceive('isFile')
            ->withArgs([base_path("$rootDir/tracker")])
            ->andReturn(true)
        ;

        /** @var ModuleManager $uut */
        $uut = $this->app->make('module.service.manager');

        // The the modules should be considered to be initialised
        $this->assertTrue($uut->isInitialised());
    }

    public function testCheckingInitialisationIfThereIsNoConfig () : void
    {
        $filesystem = $this->getMockFilesystem();

        // I the config does not specify a module root dir
        Config::shouldReceive('get')->withArgs(['modules.root', null])->andReturn(null);

        /** @var ModuleManager $uut */
        $uut = $this->app->make('module.service.manager');

        // The the modules should be considered to be initialised
        $this->assertFalse($uut->isInitialised());
    }

    public function testCheckingInitialisationIfThereIsNoTrackerFile () : void
    {
        $filesystem = $this->getMockFilesystem();

        // I the config specifies a module root dir
        $rootDir = "root_dir";
        Config::shouldReceive('get')->withArgs(['modules.root', null])->andReturn($rootDir);

        // But there isn't a modules tracker file
        $filesystem
            ->shouldReceive('isFile')
            ->withArgs([base_path("$rootDir/tracker")])
            ->andReturn(false)
        ;

        /** @var ModuleManager $uut */
        $uut = $this->app->make('module.service.manager');

        // The the modules should be considered to be initialised
        $this->assertFalse($uut->isInitialised());
    }
}
