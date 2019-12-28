<?php

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
// todo: make this a contract
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Services\RouteSource;

abstract class ServiceProviderFactory extends FileFactory
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
     */
    public function create (string $module)
    {
        $this->populateFile(base_path($this->getServiceProviderDir($module)), $this->getFileName(), $this->getStub(), [
            $this->getNamespacePlaceholder() => $this->moduleManager->getModuleNameSpace($module) . $this->getProvidersDirectory(),
            $this->getClassNamePlaceholder() => $this->getClassName(),
        ]);
    }

    /**
     * Get the relative module file directory
     *
     * @param string $module
     * @return string
     */
    protected function getRelativeModuleFileDir (string $module) : string
    {
        return config('modules.root') . "/" . ucfirst($module) . "/" . $this->routeSource->getRouteRootDir();
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
        return config("modules.root") . "/{$this->moduleNameToModuleDirName($module)}/Providers/";
    }

    /**
     * Get the module directory name of a given module
     *
     * @param string $module
     * @return string
     */
    protected function moduleNameToModuleDirName (string $module) : string
    {
        return ucfirst(strtolower($module));
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
    protected function getProvidersDirectory () : string
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
