<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class BroadcastServiceProviderFactory extends ServiceProviderFactory
{
    /**
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create (string $module) : void
    {
        $relativePath = $this->getModuleRoutesRoot($module);

        $this->populateFile($this->getServiceProviderDir($module), $this->getFileName(), $this->getStub(), [
            $this->getNamespacePlaceholder() => $this->moduleManager->getModuleNamespace($module) . $this->getProvidersRoot(),
            $this->getClassNamePlaceholder() => $this->getClassName(),
            $this->getRouteFilePlaceholder() => $this->getRelativeRouteFile($relativePath),
        ]);
    }

    /**
     * Get the placeholder for the route file
     *
     * @return string
     */
    protected function getRouteFilePlaceholder () : string
    {
        return "{routeFile}";
    }

    protected function getRelativeRouteFile (string $relativePath) : string
    {
        return $relativePath . "/" . $this->routeSource->getChannelsRoute() . $this->routeSource->getRouteFileExtension();
    }

    /**
     * Get the route service provider stub
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/broadcastServiceProvider.stub';
    }

    /**
     * Get the route service provider classname
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return "BroadcastServiceProvider";
    }
}
