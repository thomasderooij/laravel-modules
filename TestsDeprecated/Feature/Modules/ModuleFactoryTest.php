<?php

namespace TestsDeprecated\Feature\Modules;

use App\User;

class ModuleFactoryTest extends ModuleTest
{
    public function testICanUseTheFactoryWhenItIsInAModule () : void
    {
        // If I initiate modules
        $this->initModules();

        $model = User::class;

        // And I create a module
        $module = "TestModule";
        $this->createModule($module);

        // And the module is in my workbench
        $this->moduleManager->setWorkbench($module);

        // And I make a factory
        $factory = "NewFactory";
        $this->artisan("make:factory", ["name" => $factory, "--module" => $module, "--model" => $model]);

        $name = uniqid();

        // Then I should be able to call the factory function to create a model
        factory($model)->create(["name" => $name]);

        // And there should be an entry in the database
        $this->assertCount(1, User::where("name", "=", $name)->get());
        \DB::table("users")->truncate();
    }
}
