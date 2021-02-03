<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Contracts\Factories\RouteServiceProviderFactory as Contract;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

class RouteServiceProviderFactory extends ServiceProviderFactory implements Contract
{
    /**
     * Create a route service provider for your module
     *
     * @param string $module
     * @throws FileNotFoundException
     * @throws ConfigFileNotFoundException
     */
    public function create (string $module) : void
    {
        $relativePath = $this->getModuleRoutesRoot($module);

        $this->populateFile($this->getServiceProviderDir($module), $this->getFileName(), $this->getStub(), [
            $this->getNamespacePlaceholder() => $this->moduleManager->getModuleNamespace($module) . $this->getProvidersRoot(),
            $this->getControllerNamespacePlaceholder() => $this->moduleManager->getModuleNamespace($module, false),
            $this->getClassNamePlaceholder() => $this->getClassName(),
            $this->getWebRouteFilePlaceholder() => $this->getWebFile($relativePath),
            $this->getApiRouteFilePlaceholder() => $this->getApiFile($relativePath),
        ]);
    }

    /**
     * Get the route service provider stub
     *
     * @return string
     */
    protected function getStub () : string
    {
        return __DIR__ . '/stubs/routeServiceProvider.stub';
    }

    /**
     * Get the relative web file
     *
     * @param string $relativePath
     * @return string
     */
    protected function getWebFile (string $relativePath) : string
    {
        return $relativePath . "/" . $this->routeSource->getWebRoute() . $this->routeSource->getRouteFileExtension();
    }

    /**
     * Get the relative api file
     *
     * @param string $relativePath
     * @return string
     */
    protected function getApiFile (string $relativePath) : string
    {
        return $relativePath . "/" . $this->routeSource->getApiRoute() . $this->routeSource->getRouteFileExtension();
    }

    /**
     * Get the route service provider classname
     *
     * @return string
     */
    protected function getClassName () : string
    {
        return "RouteServiceProvider";
    }

    /**
     * Get the controller namespace placeholder
     *
     * @return string
     */
    protected function getControllerNamespacePlaceholder () : string
    {
        return "{controllerNamespace}";
    }

    /**
     * Get the web route file placeholder
     *
     * @return string
     */
    protected function getWebRouteFilePlaceholder () : string
    {
        return "{webRouteFile}";
    }

    /**
     * Get the api route file placeholder
     *
     * @return string
     */
    protected function getApiRouteFilePlaceholder () : string
    {
        return "{apiRouteFile}";
    }
}
