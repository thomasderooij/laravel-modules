<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Factories\ConsoleKernelFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ControllerFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Factories\RouteFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\HttpKernelFactory;
use Thomasderooij\LaravelModules\Contracts\Factories\ServiceProviderFactory;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;

class ModuleFactory implements Contract
{
    /**
     * The route factory
     */
    protected RouteFactory $routeFactory;

    /**
     * The auth service provider factory
     */
    protected ServiceProviderFactory $authServiceProviderFactory;

    /**
     * The broadcast service provider factory
     */
    protected ServiceProviderFactory $broadcastServiceProviderFactory;

    /**
     * The event service provider factory
     */
    protected ServiceProviderFactory $eventServiceProviderFactory;

    /**
     * The route service provider factory
     */
    protected ServiceProviderFactory $routeServiceProviderFactory;

    /**
     * The console kernel factory
     */
    protected ConsoleKernelFactory $consoleKernelFactory;

    /**
     * The http kernel factory
     */
    protected HttpKernelFactory $httpKernelFactory;

    /**
     * The base controller factory
     */
    protected ControllerFactory $controllerFactory;

    /**
     * The module managing service
     */
    protected ModuleManager $moduleManager;

    protected Filesystem $files;

    public function __construct(
        Filesystem $files,
        RouteFactory $routeFactory,
        ServiceProviderFactory $routeServiceProviderFactory,
        ConsoleKernelFactory $consoleKernelFactory,
        HttpKernelFactory $httpKernelFactory,
        ControllerFactory $controllerFactory,
        ServiceProviderFactory $authServiceProviderFactory,
        ServiceProviderFactory $broadcastServiceProviderFactory,
        ServiceProviderFactory $eventServiceProviderFactory,
        ModuleManager $moduleManager
    )
    {
        $this->files = $files;
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
     * @throws ModuleCreationException
     * @throws ModulesNotInitialisedException
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

        if (!$this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
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

        return base_path("{$root}/" . $module);
    }
}
