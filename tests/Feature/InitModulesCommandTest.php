<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;

class InitModulesCommandTest extends CommandTest
{
    private $root = "MyModules";

    public function testInitModules () : void
    {
        $filesystem = new Filesystem();
        // When I init the modules
        $response = $this->artisan("module:init");
        // I should be asked what my modules root directory will be
        $response->expectsQuestion("What will be the root directory of your modules?", $this->root);

        // We config should check for a modules root, and return null
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);

        // Building the app bootstrap file
        $this->filesystem->shouldReceive("move")->withArgs([base_path("bootstrap/app.php"), base_path("bootstrap/app_orig.php")])->once();
        $bootstrapStubLocation = realpath(__DIR__ . "/../../src/Factories/stubs/bootstrapFile.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$bootstrapStubLocation])->andReturn($filesystem->get($bootstrapStubLocation))->once();
        $newBootstrapFileContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("bootstrap/app.php"), Mockery::capture($newBootstrapFileContent)])->once();

        // Creating the config file
        $configStubLocation = realpath(__DIR__ . "/../../src/Factories/stubs/config.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$configStubLocation])->andReturn($filesystem->get($configStubLocation))->once();
        $configFileContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([config_path("modules.php"), Mockery::capture($configFileContent)])->once();
        $appFileContent = null;
        $this->filesystem->shouldReceive("get")->withArgs([config_path("app.php")])->andReturn($filesystem->get(config_path("app.php")))->once();
        $this->filesystem->shouldReceive("put")->withArgs([config_path("app.php"), Mockery::capture($appFileContent)])->once();

        // Creating the tracker file
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path($this->root), 0755, true]);
        $trackerFileLocation = realpath(__DIR__ . "/../../src/Factories/stubs/tracker.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$trackerFileLocation])->andReturn($filesystem->get($trackerFileLocation))->once();
        $trackerFileContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$this->root/.tracker"), Mockery::capture($trackerFileContent)])->once();

        // Add namespace to autoload
        $composerFileLocation = base_path("composer.json");
        $this->filesystem->shouldReceive("get")->withArgs([$composerFileLocation])->andReturn($filesystem->get($composerFileLocation))->once();
        $composerFileContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([$composerFileLocation, Mockery::capture($composerFileContent)]);

        // Creating the migration
        $migrationStubFileLocation = realpath(__DIR__ . "/../../src/Factories/stubs/moduleMigration.stub");
        $this->filesystem->shouldReceive("get")->withArgs([$migrationStubFileLocation])->andReturn($filesystem->get($migrationStubFileLocation))->once();
        $migrationFileContent = null;
        $this->filesystem->shouldReceive("put")->withArgs([database_path("migrations/2010_11_01_000000_module_init_migration.php"), Mockery::capture($migrationFileContent)])->once();

        // And I expect to receive instructions after a successful initialisation
        $response->expectsOutput("You are set to go. Make sure to run migration command to get your module migrations working.");
        $response->expectsOutput("Call for module:new your-module-name-here to create a module. For any other info, check out the readme.md file.");

        // Dumping autoloads
        $this->filesystem->shouldReceive("exists")->withArgs([base_path("composer.phar")])->andReturn(false);

        // Running the actual command
        $response->run();
        $this->assertMatchesSnapshot($newBootstrapFileContent);
        $this->assertMatchesSnapshot($configFileContent);
        $this->assertMatchesSnapshot($appFileContent);
        $this->assertMatchesSnapshot($trackerFileContent);
        $this->assertMatchesSnapshot($composerFileContent);
    }

    public function testModulesAreAlreadyInitialised () : void
    {
        // When I init the modules
        $response = $this->artisan("module:init");

        // We config should check for a modules root, and return a root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn("Modules");

        // And the file system should check for a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("Modules/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("Modules/.tracker")])->andReturn(json_encode(["modules" => [], "activeModules" => []]));

        // I expect to be told the modules are already initialised
        $response->expectsOutput("Modules are already initiated.");

        $response->run();
    }
}
