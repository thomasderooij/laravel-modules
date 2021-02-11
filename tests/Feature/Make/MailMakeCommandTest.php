<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Make;

use Illuminate\Support\Facades\Cache;
use Mockery;

class MailMakeCommandTest extends MakeTest
{
    public function testWithoutModule () : void
    {
        // If I want to make a mail for my module
        $response = $this->artisan("make:mail", ["name" => $mail = "MyNewMail"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this mail already exists
        $fileDirectory = "Mail";
        $fileName = "$mail.php";
        $this->setFileExpectations($fileDirectory, $fileName, false);

        // The mail stub should be fetched
        $this->fetchStub();

        // The mail should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithModule () : void
    {
        // If I want to make a mail for my module
        // The casing of the module name differs from the one in the tracker file to ensure casing does not matter for the module option
        $response = $this->artisan("make:mail", ["name" => $mail = "MyNewMail", "--module" => $module = "MyModule"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this mail already exists
        $fileDirectory = "Mail";
        $fileName = "$mail.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The mail stub should be fetched
        $this->fetchStub();

        // The mail should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithWorkbench () : void
    {
        // If I want to make a mail for my module
        $response = $this->artisan("make:mail", ["name" => $mail = "MyNewMail"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule();

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(["workbench" => $this->module]);

        // We should then check if this mail already exists
        $fileDirectory = "Mail";
        $fileName = "$mail.php";
        $this->setFileExpectations($fileDirectory, $fileName, true);

        // The mail stub should be fetched
        $this->fetchStub();

        // The mail should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("{$this->modulesDir}/{$this->module}/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    public function testWithVanillaModule () : void
    {
        // If I want to make a mail for my module
        $response = $this->artisan("make:mail", ["name" => $mail = "MyNewMail", "--module" => $module = "MYMODULE"]);

        // We make sure the modules are initialised
        $this->initialisedModulesSetup();
        $this->vanillaModule($module);

        // And the workbench should be checked
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);

        // We should then check if this mail already exists
        $fileDirectory = "Mail";
        $fileName = "$mail.php";
        $this->setFileExpectations($fileDirectory, $fileName, false);

        // The mail stub should be fetched
        $this->fetchStub();

        // The mail should then be created
        $capture = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("app/$fileDirectory/$fileName"), Mockery::capture($capture)]);

        $response->run();

        $this->assertMatchesSnapshot($capture);
    }

    protected function fetchStub(): void
    {
        $stub = realpath(__DIR__ . "/../../../vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/mail.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$stub])->andReturn($this->files->get($stub));
    }
}
