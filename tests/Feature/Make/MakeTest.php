<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Tests\Feature\CommandTest;

abstract class MakeTest extends CommandTest
{
    protected $files;
    protected $modulesDir;
    protected $module;

    protected function setUp(): void
    {
        parent::setUp();

//        Cache::shouldReceive("driver")->andReturn(new Repository(new FileStore($this->app['files'], base_path("storage/cache"))))->once();
        // The config should get the auth guard
        Config::shouldReceive("get")->withArgs(["auth.defaults.guard"])->andReturn("api");
        // And provider
        Config::shouldReceive("get")->withArgs(["auth.guards.api.provider"])->andReturn("users");
        // And then get the user model
        Config::shouldReceive("get")->withArgs(["auth.providers.users.model"])->andReturn("App\Models\User");

        $this->files = new Filesystem();
    }

    protected function initialisedModulesSetup () : void
    {
        // I should be asked for the modules root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($this->modulesDir = "MyModulesDir");

        // And the filesystem should get the tracker data
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("{$this->modulesDir}/.tracker")])->andReturn(true);
        // It should then get the tracker content
        $this->filesystem->shouldReceive("get")->withArgs([base_path("{$this->modulesDir}/.tracker")])->andReturn(json_encode([
            "modules" => ["module_1", $this->module = "MYMODULE"],
            "activeModules" => [$this->module]
        ]));
    }

    protected function vanillaModule (string $vanilla = "vanilla") : void
    {
        // It should check what the vanilla module is in the config
        Config::shouldReceive("get")->withArgs(["modules.vanilla", null])->andReturn($vanilla);
    }

    protected function setFileExpectations (?string $appFileDirectory, string $fileName, bool $module = true, string $base = null, bool $checkIfFileExists = true)
    {
        if ($base === null) {
            $base = $module ? "{$this->modulesDir}/{$this->module}" : "app";
        }

        $directory = $appFileDirectory === null ? "$base" : "$base/$appFileDirectory";

        if ($checkIfFileExists) {
            $this->filesystem->shouldReceive("exists")->withArgs([base_path("$directory/$fileName")])->andReturn(false)->once();
        }

        // We should then check if the directory exists
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$directory")])->andReturn(false);
        // And create the directory if needed
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$directory"), 0777, true, true]);
    }

    abstract protected function fetchStub () : void;
}
