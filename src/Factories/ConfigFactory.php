<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\CompositeProviders\AuthCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\BroadcastCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\EventCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\RouteCompositeServiceProvider;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Database\Factories\HasFactory;

class ConfigFactory extends FileFactory implements Contract
{
    public function __construct(Filesystem $filesystem, ModuleManager $moduleManager)
    {
        parent::__construct($filesystem, $moduleManager);

        $this->moduleManager = $moduleManager;
    }

    /**
     * Create modules config files and metadata files
     *
     * @param string $appNamespace
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function create(string $appNamespace, string $rootDir): void
    {
        $this->createConfigFile($appNamespace, $rootDir);
        $this->replaceServiceProviders($appNamespace);
    }

    /**
     * Undo the creation of the config files and metadata
     *
     * @throws FileNotFoundException
     */
    public function undo(): void
    {
        $this->revertServiceProviders(config('modules.app_namespace'));
        $this->removeConfigFile();
    }

    /**
     * Remove the modules config file
     */
    protected function removeConfigFile(): void
    {
        $this->filesystem->delete(base_path('config') . "/{$this->getConfigFileName()}");
    }

    /**
     * Create a modules config file
     *
     * @param string $appNamespace
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    protected function createConfigFile(string $appNamespace, string $rootDir): void
    {
        $this->populateFile(base_path("config"), $this->getConfigFileName(), $this->getStub(), [
            $this->getAppNamespacePlaceholder() => $appNamespace,
            $this->getModuleDirPlaceholder() => $rootDir,
            $this->getModuleNamespacePlaceholder() => ucfirst($rootDir),
            $this->getModuleAutoloadPlaceholder() => $rootDir,
            $this->getVanillaModuleNamePlaceholder() => $this->getDefaultVanillaModuleName(),
            $this->getModuleBaseModelPlaceholder() => $this->getDefaultModel(),
            $this->getModuleHasFactoryPlaceholder() => $this->getDefaultHasFactoryTrait(),
            $this->getCacheValidityPlaceholder() => (string) $this->getCacheValidity(),
        ]);
    }

    /**
     * Replace the service providers in the config/app.php file with their composite counterparts.
     *
     * @throws FileNotFoundException
     */
    protected function replaceServiceProviders(string $appNamespace): void
    {
        $this->populateFile(
            config_path(),
            "app.php",
            config_path("app.php"),
            $this->getServiceProvidersArray($appNamespace )
        );
    }

    /**
     * Replace the service providers in the config/app.php file with their original providers
     *
     * @throws FileNotFoundException
     */
    protected function revertServiceProviders(string $appNamespace): void
    {
        $this->populateFile(
            config_path(),
            "app.php",
            config_path("app.php"),
            array_flip($this->getServiceProvidersArray($appNamespace))
        );
    }

    protected function getServiceProvidersArray(string $appNamespace): array
    {
        return [
            "$appNamespace\Providers\AuthServiceProvider" => AuthCompositeServiceProvider::class,
            "$appNamespace\Providers\BroadcastServiceProvider" => BroadcastCompositeServiceProvider::class,
            "$appNamespace\Providers\EventServiceProvider" => EventCompositeServiceProvider::class,
            "$appNamespace\Providers\RouteServiceProvider" => RouteCompositeServiceProvider::class,
        ];
    }

    /**
     * Get the location of the file stub
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . "/stubs/config.stub";
    }

    /**
     * Get the tracker stub file location
     *
     * @return string
     */
    protected function getTrackerStub(): string
    {
        return __DIR__ . '/stubs/tracker.stub';
    }

    protected function getAppNamespacePlaceholder(): string
    {
        return "{appNamespace}";
    }

    /**
     * Get the module stub directory placeholder
     *
     * @return string
     */
    protected function getModuleDirPlaceholder(): string
    {
        return "{moduleDirectory}";
    }

    /**
     * Get the module stub namespace placeholder
     *
     * @return string
     */
    protected function getModuleNamespacePlaceholder(): string
    {
        return "{moduleDirectoryUcfirst}";
    }

    /**
     * Get the composer psr4 autoload placeholder
     *
     * @return string
     */
    protected function getModuleAutoloadPlaceholder(): string
    {
        return "{moduleAutoload}";
    }

    /**
     * Get the module base model placeholder
     *
     * @return string
     */
    protected function getModuleBaseModelPlaceholder(): string
    {
        return "{moduleModel}";
    }

    /**
     * Get the HasFactory trait placeholder
     *
     * @return string
     */
    protected function getModuleHasFactoryPlaceholder(): string
    {
        return "{moduleHasFactory}";
    }

    /**
     * Get the cache validity placeholder
     *
     * @return string
     */
    protected function getCacheValidityPlaceholder(): string
    {
        return "{cacheValidity}";
    }

    /**
     * Get the cache validity. Defaults to one week.
     *
     * @return int
     */
    protected function getCacheValidity(): int
    {
        return 60 * 60 * 24 * 7;
    }

    /**
     * Get the modules config filename
     *
     * @return string
     */
    public function getConfigFileName(): string
    {
        return "modules.php";
    }

    /**
     * Get the directory in which module metadata gets stored
     *
     * @return string
     */
    public function getModuleRoot(string $rootDir): string
    {
        return base_path($rootDir);
    }

    /**
     * Get the vanilla module name placeholder
     *
     * @return string
     */
    public function getVanillaModuleNamePlaceholder(): string
    {
        return "{vanillaModule}";
    }

    /**
     * Get the default value for the vanilla module
     *
     * @return string
     */
    public function getDefaultVanillaModuleName(): string
    {
        return "Vanilla";
    }

    public function getDefaultModel(): string
    {
        return Model::class;
    }

    public function getDefaultHasFactoryTrait(): string
    {
        return HasFactory::class;
    }
}
