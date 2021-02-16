<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\MigrateCommand;

class MigrateCommandTest extends MigrateTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // We should get our workbench for the kernel
        $this->moduleManager->shouldReceive('getWorkbench')->andReturn(null);
    }

    public function testWithModules () : void
    {
        // If there are modules
        $modules = [$module1 = "module_1", $module2 = "module_2"];
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        /** @var Mockery\MockInterface&MigrateCommand $command */
        $command = Mockery::mock(MigrateCommand::class."[parentCall, getMigrationPaths,prepareDatabase]", [
            $this->migrator,
            $this->dispatcher,
            $this->moduleManager
        ]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance("command.migrate", $command);

        // The database should be prepped
        $command->shouldReceive("prepareDatabase");

        // There should be a repository
        $this->migrator->shouldReceive("repositoryExists")->andReturn(true);

        // We should have the modules
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module1])->andReturn(true);
        $this->moduleManager->shouldReceive("hasModule")->withArgs([$module2])->andReturn(true);


        // We should get output from the migrator
        $command->shouldReceive("option")->withArgs(["predent"])->andReturn($pretend = false);
        $command->shouldReceive("option")->withArgs(["step"])->andReturn($step = false);
        $this->migrator->shouldReceive("setOutput")->withArgs([Mockery::any()])->andReturn($this->migrator);

        $command->shouldReceive("getMigrationPaths")->withArgs([$module1])->andReturn($paths = ["paths", "here"]);
        $command->shouldReceive("getMigrationPaths")->withArgs([$module2])->andReturn($paths);
        $this->migrator->shouldReceive("run")->withArgs([$paths, ["pretend" => $pretend, "step" => $step, "module" => $module1]]);
        $this->migrator->shouldReceive("run")->withArgs([$paths, ["pretend" => $pretend, "step" => $step, "module" => $module2]]);

        // And there should be no seed option
        $this->migrator->shouldReceive("option")->withArgs(["seed"])->andReturn(null);

        // If I want to do a good old fashioned migrate
        $response = $this->artisan("migrate", ["--modules" => implode(",", $modules)]);

        $response->run();
    }

    public function testWithoutModules () : void
    {
        // If there are modules
        $modules = [$module1 = "module_1", $module2 = "module_2"];
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        /** @var Mockery\MockInterface&MigrateCommand $command */
        $command = Mockery::mock(MigrateCommand::class."[parentCall, getMigrationPaths,prepareDatabase]", [
            $this->migrator,
            $this->dispatcher,
            $this->moduleManager
        ]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance("command.migrate", $command);

        // The database should be prepped
        $command->shouldReceive("prepareDatabase");

        // There should be a repository
        $this->migrator->shouldReceive("repositoryExists")->andReturn(true);

        // It should call its parent function
        $command->shouldReceive("parentCall")->withArgs(["handle"]);

        // If I want to do a good old fashioned migrate
        $response = $this->artisan("migrate");

        $response->run();
    }
}
