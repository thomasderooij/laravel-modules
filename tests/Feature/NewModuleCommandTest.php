<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;

class NewModuleCommandTest extends CommandTest
{
    public function testCreateNewModule(): void
    {
        $filesystem = new Filesystem();

        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");
        Config::shouldReceive('get')->withArgs(['modules.cache_validity', null])->andReturn($validity = 123);

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        // Get its contents
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
        // These 2 are for the composite and http kernel
            json_encode(["modules" => [$otherModule = "OtherModule"], "activeModules" => [$otherModule]]),
            json_encode(["modules" => [$otherModule = "OtherModule"], "activeModules" => [$otherModule]]),
            // This one is from the new module command
            json_encode(["modules" => [$otherModule = "OtherModule"], "activeModules" => [$otherModule]]),
            // And this one from the add dependency command
            json_encode([
                "modules" => [$otherModule = "OtherModule", $newModule],
                "activeModules" => [$otherModule, $newModule],
            ])
        );

        // Creating the modules root directory
        // See if the module directory exists
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root")])->times(3)->andReturn(
            false,
            false,
            true
        );
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root/$newModule")])->andReturn(
            false
        )->once();
        // And if not, create it
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root"), 0755, true]);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule"), 0755, true]);

        // Creating routes files
        // We will do the same with the routes directory
        $this->filesystem->shouldReceive("exists")->withArgs([base_path("$root/$newModule/routes")])->andReturn(false);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/routes"), 0755, true]);
        // Then we will get the stubs for the routes
        $this->filesystem->shouldReceive("get")
            ->withArgs([$commonStub = realpath(__DIR__ . "/../../src/Factories/stubs/routes/common.stub")])
            ->andReturn($filesystem->get($commonStub));
        $this->filesystem->shouldReceive("get")
            ->withArgs([$consoleStub = realpath(__DIR__ . "/../../src/Factories/stubs/routes/console.stub")])
            ->andReturn($filesystem->get($consoleStub));

        $this->filesystem->shouldReceive("get")
            ->withArgs([$emptyStub = realpath(__DIR__ . "/../../src/Factories/stubs/routes/empty.stub")])
            ->andReturn($filesystem->get($emptyStub));
        $webFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/routes/web.php"), Mockery::capture($webFile)]
        )->once();
        $apiFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/routes/api.php"), Mockery::capture($apiFile)]
        )->once();
        $consoleFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/routes/console.php"), Mockery::capture($consoleFile)]
        )->once();
        $channelsFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/routes/channels.php"), Mockery::capture($channelsFile)]
        )->once();

        // Creating console files
        $this->filesystem->shouldReceive("get")
            ->withArgs([$consoleKernelStub = realpath(__DIR__ . "/../../src/Factories/stubs/consoleKernel.stub")])
            ->andReturn($filesystem->get($consoleKernelStub));
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Console"), 0755, true]
        );
        $consoleKernelFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/Console/Kernel.php"), Mockery::capture($consoleKernelFile)]
        );

        // Creating http files
        $this->filesystem->shouldReceive("get")
            ->withArgs([$httpKernelStub = realpath(__DIR__ . "/../../src/Factories/stubs/httpKernel.stub")])
            ->andReturn($filesystem->get($httpKernelStub));
        $this->filesystem->shouldReceive("get")
            ->withArgs([$controllerStub = realpath(__DIR__ . "/../../src/Factories/stubs/controller.stub")])
            ->andReturn($filesystem->get($controllerStub));

        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Http"), 0755, true]);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs(
            [base_path("$root/$newModule/Http/Controllers"), 0755, true]
        );
        $httpKernelFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/Http/Kernel.php"), Mockery::capture($httpKernelFile)]
        );
        $controllerFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [base_path("$root/$newModule/Http/Controllers/Controller.php"), Mockery::capture($controllerFile)]
        );

        // Creating service providers
        $this->filesystem->shouldReceive("makeDirectory")->withArgs(
            [base_path("$root/$newModule/Providers"), 0755, true]
        );
        $this->filesystem->shouldReceive("get")
            ->withArgs(
                [$authServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/authServiceProvider.stub")]
            )
            ->andReturn($filesystem->get($authServiceProviderStub));
        $this->filesystem->shouldReceive("get")
            ->withArgs(
                [
                    $broadcastServiceProviderStub = realpath(
                        __DIR__ . "/../../src/Factories/stubs/broadcastServiceProvider.stub"
                    )
                ]
            )
            ->andReturn($filesystem->get($broadcastServiceProviderStub));
        $this->filesystem->shouldReceive("get")
            ->withArgs(
                [$eventServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/eventServiceProvider.stub")]
            )
            ->andReturn($filesystem->get($eventServiceProviderStub));
        $this->filesystem->shouldReceive("get")
            ->withArgs(
                [$routeServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/routeServiceProvider.stub")]
            )
            ->andReturn($filesystem->get($routeServiceProviderStub));

        $authServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [
                base_path("$root/$newModule/Providers/AuthServiceProvider.php"),
                Mockery::capture($authServiceProviderFile)
            ]
        );
        $broadcastServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [
                base_path("$root/$newModule/Providers/BroadcastServiceProvider.php"),
                Mockery::capture($broadcastServiceProviderFile)
            ]
        );
        $eventServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [
                base_path("$root/$newModule/Providers/EventServiceProvider.php"),
                Mockery::capture($eventServiceProviderFile)
            ]
        );
        $routeServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs(
            [
                base_path("$root/$newModule/Providers/RouteServiceProvider.php"),
                Mockery::capture($routeServiceProviderFile)
            ]
        );

        // We store the new module in the tracker file
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode(["modules" => ["OtherModule", $newModule], "activeModules" => ["OtherModule"]],
                JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)
        ])->once();
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode(["modules" => ["OtherModule", $newModule], "activeModules" => ["OtherModule", $newModule]],
                JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)
        ])->once();

        // Add the module to our workbench
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        $cacheInput = null;
        Cache::shouldReceive("put")->withArgs(["modules-cache", Mockery::capture($cacheInput), $validity]);

        $response->expectsOutput("Your module has been created in the $root/$newModule directory.");

        // Next we expect to be asked which modules this module depends on, and we answer on the other module
        $response->expectsChoice("Which module is \"$newModule\" dependent on?", $otherModule, [
            "0" => "None. I'm done here.",
            "1" => $otherModule
        ]);

        // We then expect the dependency to be saved
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode([
                "modules" => ["OtherModule", $newModule],
                "activeModules" => ["OtherModule", $newModule],
                "dependencies" => [
                    ["up" => $otherModule, "down" => $newModule]
                ]
            ], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)
        ])->andReturn(null);

        // After that, we expect to have another confirmation
        $response->expectsOutput("Roger that.");

        $response->run();
        $this->assertMatchesSnapshot($webFile);
        $this->assertMatchesSnapshot($apiFile);
        $this->assertMatchesSnapshot($consoleFile);
        $this->assertMatchesSnapshot($channelsFile);
        $this->assertMatchesSnapshot($consoleKernelFile);
        $this->assertMatchesSnapshot($httpKernelFile);
        $this->assertMatchesSnapshot($controllerFile);
        $this->assertMatchesSnapshot($authServiceProviderFile);
        $this->assertMatchesSnapshot($broadcastServiceProviderFile);
        $this->assertMatchesSnapshot($eventServiceProviderFile);
        $this->assertMatchesSnapshot($routeServiceProviderFile);
        $this->assertMatchesSnapshot($cacheInput);
    }

    public function testModulesNotInitialised(): void
    {
        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        $response->expectsOutput(
            "The modules need to be initialised first. You can do this by running the module:init command."
        );
        $response->run();
    }

    public function testModuleAlreadyExists(): void
    {
        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("get")->withArgs(["modules.app_namespace", "App"])->andReturn("MyNamespace");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$newModule], "activeModules" => []])
        );

        $response->expectsOutput("Module $newModule already exists.");
        $response->run();
    }
}
