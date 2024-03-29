<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Factories\RouteFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Services\RouteSource;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class RouteFactory extends FileFactory implements Contract
{
    /**
     * A route source object providing routing information
     */
    protected RouteSource $routeSource;

    public function __construct(Filesystem $filesystem, ModuleManager $moduleManager, RouteSource $routeSource)
    {
        parent::__construct($filesystem, $moduleManager);

        $this->routeSource = $routeSource;
    }

    /**
     * Create route files
     *
     * @param string $moduleName
     * @throws FileNotFoundException
     */
    public function create(string $moduleName): void
    {
        $directory = $this->getRouteDirectory($moduleName);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true);
        }

        foreach ($this->routeSource->getRouteFiles() as $routeFile) {
            $this->createRouteFile($routeFile, $directory);
        }
    }

    /**
     * Get the route directory
     *
     * @param string $module
     * @return string
     */
    protected function getRouteDirectory(string $module): string
    {
        return base_path(config('modules.root') . "/$module/" . $this->routeSource->getRouteRootDir());
    }

    /**
     * Create a route file
     *
     * @param string $type
     * @param string $directory
     * @throws FileNotFoundException
     */
    protected function createRouteFile(string $type, string $directory): void
    {
        $this->populateFile(
            $directory,
            $type . $this->routeSource->getRouteFileExtension(),
            $this->getStubByType($type),
            [
                "{typeUcfirst}" => ucfirst($type),
                "{type}" => $type,
                "{middleware}" => $type,
            ]
        );
    }

    /**
     * Get a route file stub based on its type
     *
     * @param string $type
     * @return string
     */
    protected function getStubByType(string $type): string
    {
        switch ($type) {
            case $this->routeSource->getWebRoute():
            case $this->routeSource->getApiRoute():
                return __DIR__ . '/stubs/routes/common.stub';
            case $this->routeSource->getConsoleRoute():
                return __DIR__ . '/stubs/routes/console.stub';
            default:
                return __DIR__ . '/stubs/routes/empty.stub';
        }
    }
}
