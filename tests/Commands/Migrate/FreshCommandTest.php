<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Mockery;
use Symfony\Component\Console\Input\InputOption;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\FreshCommand;

class FreshCommandTest extends MigrateTest
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMigrateFreshWithTheModulesArgument () : void
    {
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        /** @var Mockery\MockInterface&FreshCommand $command */
        $command = Mockery::mock(FreshCommand::class."[parentCall, getModules]", [$this->moduleManager]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance("command.migrate.fresh", $command);

        // If I want to do a good old fashioned migrate:fresh
        $response = $this->artisan("migrate:fresh");

        // The workbench should return something
        $this->moduleManager->shouldReceive('getWorkbench')->andReturn($module = "Module");
        // It should clear the workbench
        $this->moduleManager->shouldReceive("clearWorkbench");

        // It should call its parent handle function
        $command->shouldReceive("parentCall")->withArgs(["getOptions"])->andReturn([
            ["path", null, InputOption::VALUE_OPTIONAL, "The path(s) to the migrations files to be executed"],
            ["step", null, InputOption::VALUE_OPTIONAL, "Force the migrations to be run so they can be rolled back individually"],
            ["realpath", null, InputOption::VALUE_OPTIONAL, "Indicate any provided migration file paths are pre-resolved absolute paths"],
            ["modules", null, InputOption::VALUE_OPTIONAL, "The modules you want included in this migration."]
        ]);
        $command->shouldReceive("parentCall")->withArgs(["handle"]);
        // It should try to get the modules, but receive nothing
        $command->shouldReceive("getModules")->andReturn([]);

        // And then it should set the workbench back
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module]);

        $response->run();
    }

    public function testMigrateFreshWithoutArgumentsAndWithoutModules () : void
    {
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        $command = Mockery::mock(FreshCommand::class."[parentCall, call, getModules]", [$this->moduleManager]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance("command.migrate.fresh", $command);

        $response = $this->instance("command.migrate.fresh", $command);

        // If I want to do a good old fashioned migrate:fresh
        $module1 = "Module_1";
        $module2 = "Module_2";
        // So, apparently orchestra does not support options for the migrate fresh call, I guess. I don't know why, but we just need to make sure
        //  the getModules function returns our modules for this test, so this is kind of a workaround
        $response = $this->artisan("migrate:fresh");

        // The workbench should return something
        $this->moduleManager->shouldReceive('getWorkbench')->andReturn($module = "Module");
        // It should clear the workbench
        $this->moduleManager->shouldReceive("clearWorkbench");

        // It should call its parent handle function
        $command->shouldReceive("parentCall")->withArgs(["handle"]);
        // It should try to get the modules, and return 2 modules
        $command->shouldReceive("getModules")->andReturn([$module1, $module2]);

        // It should then set modules to the workbench, one by one
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module1]);
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module2]);
        // And the command should call its call function
        $command->shouldReceive("call")->withArgs(["migrate", ["--force" => true]]);

        // And then it should set the workbench back
        $this->moduleManager->shouldReceive("setWorkbench")->withArgs([$module]);

        $response->run();
    }
}
