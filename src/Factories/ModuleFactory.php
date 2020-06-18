<?php

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Contracts\Factories\ConsoleKernelFactory as ConsoleKernelFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ControllerFactory as ControllerFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Factories\RouteFactory as RouteFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\RouteServiceProviderFactory as RouteServiceProviderFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\AuthServiceProviderFactory as AuthServiceProviderFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\BroadcastServiceProviderFactory as BroadcastServiceProviderFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\EventServiceProviderFactory as EventServiceProviderFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\HttpKernelFactory as HttpKernelFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;

class ModuleFactory implements Contract
{
    /**
     * The route factory
     *
     * @var RouteFactoryContract
     */
    protected $routeFactory;

    /**
     * The auth service provider factory
     *
     * @var AuthServiceProviderFactoryContract
     */
    protected $authServiceProviderFactory;

    /**
     * The broadcast service provider factory
     *
     * @var BroadcastServiceProviderFactoryContract
     */
    protected $broadcastServiceProviderFactory;

    /**
     * The event service provider factory
     *
     * @var BroadcastServiceProviderFactoryContract
     */
    protected $eventServiceProviderFactory;

    /**
     * The route service provider factory
     *
     * @var RouteServiceProviderFactoryContract
     */
    protected $routeServiceProviderFactory;

    /**
     * The console kernel factory
     *
     * @var ConsoleKernelFactoryContract
     */
    protected $consoleKernelFactory;

    /**
     * The http kernel factory
     *
     * @var HttpKernelFactoryContract
     */
    protected $httpKernelFactory;

    /**
     * The base controller factory
     *
     * @var ControllerFactoryContract
     */
    protected $controllerFactory;

    /**
     * The module managing service
     *
     * @var ModuleManager
     */
    protected $moduleManager;

    public function __construct(
        RouteFactoryContract $routeFactory,
        RouteServiceProviderFactoryContract $routeServiceProviderFactory,
        ConsoleKernelFactoryContract $consoleKernelFactory,
        HttpKernelFactoryContract $httpKernelFactory,
        ControllerFactoryContract $controllerFactory,
        AuthServiceProviderFactoryContract $authServiceProviderFactory,
        BroadcastServiceProviderFactoryContract $broadcastServiceProviderFactory,
        EventServiceProviderFactoryContract $eventServiceProviderFactory,
        ModuleManager $moduleManager
    )
    {
        $this->routeFactory = $routeFactory;
        $this->routeServiceProviderFactory = $routeServiceProviderFactory;
        $this->authServiceProviderFactory = $authServiceProviderFactory;
        $this->broadcastServiceProviderFactory = $broadcastServiceProviderFactory;
        $this->eventServiceProviderFactory = $eventServiceProviderFactory;
        $this->consoleKernelFactory = $consoleKernelFactory;
        $this->httpKernelFactory = $httpKernelFactory;
        $this->controllerFactory = $controllerFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Create a new module
     *
     * @param string $module
     * @throws FileNotFoundException
     * @throws ModuleCreationException
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function create (string $module) : void
    {
        if (!$this->moduleManager->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        if ($this->moduleManager->hasModule($module)) {
            throw new ModuleCreationException("The module $module already exists.");
        }

        $this->createBaseDirectory($module);
        $this->routeFactory->create($module);
        $this->consoleKernelFactory->create($module);
        $this->httpKernelFactory->create($module);
        $this->controllerFactory->create($module);
        $this->authServiceProviderFactory->create($module);
        $this->broadcastServiceProviderFactory->create($module);
        $this->eventServiceProviderFactory->create($module);
        $this->routeServiceProviderFactory->create($module);
    }

    /**
     * Create the module base directory
     *
     * @param string $module
     */
    protected function createBaseDirectory (string $module) : void
    {
        $dir = $this->getDirName($module);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Get the absolute module directory name
     *
     * @param string $module
     * @return string
     */
    protected function getDirName (string $module) : string
    {
        $root = config('modules.root');
        $name = strtolower($module);
        $name = ucfirst($name);

        return base_path("{$root}/" . $name);
    }
}
