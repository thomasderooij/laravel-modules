<?php

namespace TestsDeprecated\Feature\Modules;

use Thomasderooij\LaravelModules\CompositeProviders\AuthCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\EventCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\RouteCompositeServiceProvider;

class InitModulesCommandTest extends ModuleTest
{
    public function testInitiatingModules () : void
    {
        // If there is a bootstrap/app.php file
        $originalFileHash = sha1_file(base_path("bootstrap/app.php"));

        // And I run the init command
        $response = $this->artisan("module:init")->expectsQuestion("What will be the root directory of your modules?", $this->root);
        $response->expectsOutput("You are set to go. Make sure to run migration command to get your module migrations working.");
        $response->expectsOutput("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");
        $response->run();

        // The bootstrap/app.php file should be renamed
        $newFileHash = sha1_file(base_path("bootstrap/app_orig.php"));
        $this->assertSame($originalFileHash, $newFileHash);

        // The app.php file should be replaced with another file
        $newAppHash = sha1_file(base_path("bootstrap/app.php"));
        $this->assertNotSame($originalFileHash, $newAppHash);

        // There should be a tracker file
        $this->assertTrue(is_file(base_path("{$this->root}/tracker")));

        // And the config should have the root module specified
        $this->refreshApplication();
        $this->assertSame($this->root, config("modules.root"));

        // And the service provider in the app.php file should be replaced with composite providers
        $this->assertNotNull(strpos($this->files->get(config_path("app.php")), AuthCompositeServiceProvider::class));
//        $this->assertNotNull(strpos($this->files->get(config_path("app.php")), BroadcastCompositeServiceProvider::class));
        $this->assertNotNull(strpos($this->files->get(config_path("app.php")), EventCompositeServiceProvider::class));
        $this->assertNotNull(strpos($this->files->get(config_path("app.php")), RouteCompositeServiceProvider::class));

        // And there should be a migration for the modules
        $this->assertFileExists(database_path("migrations/2019_11_01_000000_module_init_migration.php"));
    }

    public function testModulesCanOnlyBeInitiatedOnce () : void
    {
        // If the package is initiated
        $this->initModules();

        // If I initiate it again
        $otherRootDir = "othername";
        $response = $this->artisan("module:init");

        // I should receive a message saying the package is already initiated
        $response->expectsOutput("Modules are already initiated.");

        // And the root of modules should be the original root
        $this->refreshApplication();
        $this->assertSame($this->root, config("modules.root"));

        // And there should be no trackerfile in storage for the other specified root module
        $this->assertFalse(is_file(base_path("storage/{$otherRootDir}/tracker")));
    }
}
