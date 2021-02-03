<?php

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Tests\Test;

class NewModuleCommandTest extends Test
{
    private $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance("files", $this->filesystem);
    }

    public function testCreateNewModule () : void
    {
        $filesystem = new Filesystem();

        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");
        Cache::shouldReceive("driver")->andReturn(new Repository(new FileStore($this->app['files'], base_path("storage/cache"))))->once();

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        // Get its contents
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [], "activeModules" => []])
        );

        // Creating the modules root directory
        // See if the module directory exists
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root")])->andReturn(false)->twice();
        $this->filesystem->shouldReceive("isDirectory")->withArgs([base_path("$root/$newModule")])->andReturn(false)->once();
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
            ->andReturn($filesystem->get($commonStub))
        ;
        $this->filesystem->shouldReceive("get")
            ->withArgs([$consoleStub = realpath(__DIR__ . "/../../src/Factories/stubs/routes/console.stub")])
            ->andReturn($filesystem->get($consoleStub))
        ;

        $this->filesystem->shouldReceive("get")
            ->withArgs([$emptyStub = realpath(__DIR__ . "/../../src/Factories/stubs/routes/empty.stub")])
            ->andReturn($filesystem->get($emptyStub))
        ;
        $webFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/routes/web.php"), Mockery::capture($webFile)])->once();
        $apiFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/routes/api.php"), Mockery::capture($apiFile)])->once();
        $consoleFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/routes/console.php"), Mockery::capture($consoleFile)])->once();
        $channelsFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/routes/channels.php"), Mockery::capture($channelsFile)])->once();

        // Creating console files
        $this->filesystem->shouldReceive("get")
            ->withArgs([$consoleKernelStub = realpath(__DIR__ . "/../../src/Factories/stubs/consoleKernel.stub")])
            ->andReturn($filesystem->get($consoleKernelStub))
        ;
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Console"), 0755, true]);
        $consoleKernelFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Console/Kernel.php"), Mockery::capture($consoleKernelFile)]);

        // Creating http files
        $this->filesystem->shouldReceive("get")
            ->withArgs([$httpKernelStub = realpath(__DIR__ . "/../../src/Factories/stubs/httpKernel.stub")])
            ->andReturn($filesystem->get($httpKernelStub))
        ;
        $this->filesystem->shouldReceive("get")
            ->withArgs([$controllerStub = realpath(__DIR__ . "/../../src/Factories/stubs/controller.stub")])
            ->andReturn($filesystem->get($controllerStub))
        ;

        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Http"), 0755, true]);
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Http/Controllers"), 0755, true]);
        $httpKernelFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Http/Kernel.php"), Mockery::capture($httpKernelFile)]);
        $controllerFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Http/Controllers/Controller.php"), Mockery::capture($controllerFile)]);

        // Creating service providers
        $this->filesystem->shouldReceive("makeDirectory")->withArgs([base_path("$root/$newModule/Providers"), 0755, true]);
        $this->filesystem->shouldReceive("get")
            ->withArgs([$authServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/authServiceProvider.stub")])
            ->andReturn($filesystem->get($authServiceProviderStub))
        ;
        $this->filesystem->shouldReceive("get")
            ->withArgs([$broadcastServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/broadcastServiceProvider.stub")])
            ->andReturn($filesystem->get($broadcastServiceProviderStub))
        ;
        $this->filesystem->shouldReceive("get")
            ->withArgs([$eventServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/eventServiceProvider.stub")])
            ->andReturn($filesystem->get($eventServiceProviderStub))
        ;
        $this->filesystem->shouldReceive("get")
            ->withArgs([$routeServiceProviderStub = realpath(__DIR__ . "/../../src/Factories/stubs/routeServiceProvider.stub")])
            ->andReturn($filesystem->get($routeServiceProviderStub))
        ;

        $authServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Providers/AuthServiceProvider.php"), Mockery::capture($authServiceProviderFile)]);
        $broadcastServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Providers/BroadcastServiceProvider.php"), Mockery::capture($broadcastServiceProviderFile)]);
        $eventServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Providers/EventServiceProvider.php"), Mockery::capture($eventServiceProviderFile)]);
        $routeServiceProviderFile = null;
        $this->filesystem->shouldReceive("put")->withArgs([base_path("$root/$newModule/Providers/RouteServiceProvider.php"), Mockery::capture($routeServiceProviderFile)]);

        // We store the new module in the tracker file
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode(["modules" => [$newModule], "activeModules" => []], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)
        ])->once();
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("$root/.tracker"),
            json_encode(["modules" => [$newModule], "activeModules" => [$newModule]], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES)
        ])->once();

        // Add the module to our workbench
        Cache::shouldReceive("get")->withArgs(["modules-cache"])->andReturn(null);
        $cacheInput = null;
        Cache::shouldReceive("put")->withArgs(["modules-cache", Mockery::capture($cacheInput), 604800]);

        $response->expectsOutput("Your module has been created in the $root/$newModule directory.");

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

    public function testModulesNotInitialised () : void
    {
        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        $root = "Root";
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn(null);
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(false);

        $response->expectsOutput("The modules need to be initialised first. You can do this by running the module:init command.");
        $response->run();
    }

    public function testModuleAlreadyExists () : void
    {
        $newModule = "NewModule";
        // In order to create a new module
        $response = $this->artisan("module:new", ["name" => $newModule]);

        // The configuration should know its root
        Config::shouldReceive("get")->withArgs(["modules.root", null])->andReturn($root = "Root");
        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");

        // We should have a tracker file
        $this->filesystem->shouldReceive("isFile")->withArgs([base_path("$root/.tracker")])->andReturn(true);
        $this->filesystem->shouldReceive("get")->withArgs([base_path("$root/.tracker")])->andReturn(
            json_encode(["modules" => [$newModule], "activeModules" => []])
        );

        $response->expectsOutput("Module $newModule already exists.");
        $response->run();
    }
}
