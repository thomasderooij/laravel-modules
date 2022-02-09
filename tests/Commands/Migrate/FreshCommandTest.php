<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands\Migrate;

use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\Migrate\FreshCommand;

class FreshCommandTest extends MigrateTest
{
    public function testMigrateFreshWithoutTheModulesOption () : void
    {
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        /** @var Mockery\MockInterface&FreshCommand $command */
        $command = Mockery::mock(FreshCommand::class."[parentCall]", [$this->moduleManager, $this->dependencyHandler]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance(\Illuminate\Database\Console\Migrations\FreshCommand::class, $command);

        // If I want to do a good old-fashioned migrate:fresh
        $response = $this->artisan("migrate:fresh");
        $this->moduleManager->shouldReceive('getWorkbench')->andReturn(null);
        $this->dependencyHandler->shouldReceive("getModulesInMigrationOrder")->andReturn([]);

        // It should then call a db:wipe
        $command->shouldReceive("parentCall")->withArgs(["call", ["db:wipe", ["--force" => true]]])->andReturn(1);
        // And then it should call the migrate function
        $command->shouldReceive("parentCall")->withArgs(["call", ["migrate", ["--force" => true]]])->andReturn(1);

        $response->run();
    }

    public function testMigrateFreshWithModulesOption () : void
    {
        // We mock the command partially, since we don't want to test the extended functions in a unit test
        $command = Mockery::mock(FreshCommand::class."[parentCall, call]", [$this->moduleManager, $this->dependencyHandler]);
        $command->shouldAllowMockingProtectedMethods();
        $this->instance(\Illuminate\Database\Console\Migrations\FreshCommand::class, $command);

        // If I want to do a good old fashioned migrate:fresh
        $module1 = "Module_1";
        $module2 = "Module_2";
        $response = $this->artisan("migrate:fresh", ["--modules" => "$module1,$module2"]);
        $this->moduleManager->shouldReceive('getWorkbench')->andReturn(null);

        // And the command should call its call function
        // It should then call a db:wipe
        $command->shouldReceive("parentCall")->withArgs(["call", ["db:wipe", ["--force" => true]]]);
        // And then it should call the migrate function
        $command->shouldReceive("parentCall")->withArgs(["call", ["migrate", ["--force" => true, "--modules" => "$module1,$module2"]]]);

        $response->run();
    }
}
