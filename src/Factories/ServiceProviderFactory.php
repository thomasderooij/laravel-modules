<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Factories\ServiceProviderFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Contracts\Services\RouteSource;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

abstract class ServiceProviderFactory extends FileFactory implements Contract
{
    /**
     * The module manager service
     *
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * The route source information service
     *
     * @var RouteSource
     */
    protected $routeSource;

    public function __construct(Filesystem $filesystem, ModuleManager $moduleManager, RouteSource $routeSource)
    {
        parent::__construct($filesystem, $moduleManager);

        $this->moduleManager = $moduleManager;
        $this->routeSource = $routeSource;
    }

    /**
     * Create a new file based on a stub
     *
     * @param string $module
     * @throws FileNotFoundException
     * @throws ConfigFileNotFoundException
     * @return void
     */
    public function create (string $module) : void
    {
        $this->populateFile($this->getServiceProviderDir($module), $this->getFileName(), $this->getStub(), [
            $this->getNamespacePlaceholder() => $this->moduleManager->getModuleNamespace($module) . $this->getProvidersRoot(),
            $this->getClassNamePlaceholder() => $this->getClassName(),
        ]);
    }

    /**
     * Get the relative module file directory
     *
     * @param string $module
     * @return string
     */
    protected function getModuleRoutesRoot (string $module) : string
    {
        return $this->moduleManager->getModuleRoot($module) . "/" . $this->routeSource->getRouteRootDir();
    }

    /**
     * Get the route service provider stub
     *
     * @return string
     */
    abstract protected function getStub () : string;

    /**
     * Get the route service provider classname
     *
     * @return string
     */
    abstract protected function getClassName () : string;

    /**
     * Get the service provider directory for a given module
     *
     * @param string $module
     * @return string
     */
    protected function getServiceProviderDir (string $module) : string
    {
        return "{$this->moduleManager->getModuleDirectory($module)}/{$this->getProvidersRoot()}";
    }

    /**
     * Get the route service provider file name
     *
     * @return string
     */
    protected function getFileName () : string
    {
        return "{$this->getClassName()}.php";
    }

    /**
     * Get the providers module directory
     *
     * @return string
     */
    protected function getProvidersRoot () : string
    {
        return "Providers";
    }

    /**
     * Get the route service provider namespace placeholder
     *
     * @return string
     */
    protected function getNamespacePlaceholder () : string
    {
        return "{namespace}";
    }

    /**
     * Get the route service provider classname placeholder
     *
     * @return string
     */
    protected function getClassNamePlaceholder () : string
    {
        return "{className}";
    }
}
